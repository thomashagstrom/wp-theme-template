(function () {
    const containers = document.querySelectorAll('[data-shopify-products]');

    if (!containers.length) {
        return;
    }

    const sanitizeDomain = domain => {
        if (!domain) {
            return 'shop.example.com';
        }

        return domain.replace(/^https?:\/\//i, '').replace(/\/$/, '');
    };

    const plainText = html => {
        if (!html) {
            return '';
        }

        const temp = document.createElement('div');
        temp.innerHTML = html;
        return temp.textContent || temp.innerText || '';
    };

    const formatPrice = (value, currency, locale) => {
        if (!value) {
            return '';
        }

        const number = Number.parseFloat(value);
        if (Number.isNaN(number)) {
            return value;
        }

        try {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency
            }).format(number);
        } catch (error) {
            return `${number.toFixed(2)} ${currency}`.trim();
        }
    };

    const applyReveal = elements => {
        if (!elements.length) {
            return;
        }

        if (typeof window.editorialStarterApplyReveal === 'function') {
            window.editorialStarterApplyReveal(elements);
            return;
        }

        elements.forEach(element => {
            element.classList.add('is-visible');
        });
    };

    const buildProductUrl = (domain, product) => {
        if (product.online_store_url) {
            return product.online_store_url;
        }

        if (product.handle) {
            return `https://${domain}/products/${product.handle}`;
        }

        return `https://${domain}`;
    };

    const buildProductCard = (domain, product, options) => {
        const { currency, locale } = options;
        const article = document.createElement('article');
        article.className = 'card shopify-product is-reveal';

        const link = document.createElement('a');
        link.href = buildProductUrl(domain, product);
        link.target = '_blank';
        link.rel = 'noopener';
        link.className = 'shopify-product__media';

        const imageSrc = product?.images?.length ? product.images[0].src : '';
        if (imageSrc) {
            const image = document.createElement('img');
            image.src = imageSrc;
            image.alt = product.title || '';
            image.loading = 'lazy';
            image.decoding = 'async';
            link.appendChild(image);
        } else {
            const placeholder = document.createElement('div');
            placeholder.className = 'shopify-product__media--placeholder';
            link.appendChild(placeholder);
        }

        const content = document.createElement('div');
        content.className = 'shopify-product__content';

        const title = document.createElement('h3');
        title.className = 'shopify-product__title';
        const titleLink = document.createElement('a');
        titleLink.href = link.href;
        titleLink.target = link.target;
        titleLink.rel = link.rel;
        titleLink.textContent = product.title || '';
        title.appendChild(titleLink);

        const price = document.createElement('p');
        price.className = 'shopify-product__price';
        const primaryVariant = product?.variants?.length ? product.variants[0] : null;
        const priceLabel = primaryVariant ? formatPrice(primaryVariant.price, currency, locale) : '';
        price.textContent = priceLabel;

        const description = document.createElement('p');
        description.className = 'shopify-product__excerpt';
        const shortDescription = plainText(product.body_html).trim();
        description.textContent = shortDescription.length > 160 ? `${shortDescription.slice(0, 157)}…` : shortDescription;

        const cta = document.createElement('a');
        cta.href = link.href;
        cta.target = link.target;
        cta.rel = link.rel;
        cta.className = 'shopify-product__cta';
        cta.textContent = options.ctaLabel;

        content.appendChild(title);
        if (priceLabel) {
            content.appendChild(price);
        }
        if (shortDescription) {
            content.appendChild(description);
        }
        content.appendChild(cta);

        article.appendChild(link);
        article.appendChild(content);

        return article;
    };

    const fetchJson = async url => {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json'
            }
        });

        if (!response.ok) {
            const error = new Error(`Shopify request failed with status ${response.status}`);
            error.status = response.status;
            throw error;
        }

        return response.json();
    };

    const normalizeStorefrontProduct = product => {
        if (!product) {
            return null;
        }

        const images = Array.isArray(product?.images?.edges)
            ? product.images.edges
                  .map(edge => edge?.node)
                  .filter(Boolean)
                  .map(node => ({
                      src: node.url || '',
                      alt: node.altText || ''
                  }))
            : [];

        const variants = Array.isArray(product?.variants?.edges)
            ? product.variants.edges
                  .map(edge => edge?.node)
                  .filter(Boolean)
                  .map(node => ({
                      price:
                          node?.price?.amount !== undefined && node?.price?.amount !== null
                              ? String(node.price.amount)
                              : ''
                  }))
            : [];

        return {
            title: product.title || '',
            handle: product.handle || '',
            online_store_url: product.onlineStoreUrl || '',
            body_html: product.descriptionHtml || '',
            images,
            variants
        };
    };

    const buildStorefrontQuery = ({ handles, collectionHandle, limit }) => {
        const fragment = `
            title
            handle
            onlineStoreUrl
            descriptionHtml
            images(first: 1) {
                edges {
                    node {
                        url
                        altText
                    }
                }
            }
            variants(first: 1) {
                edges {
                    node {
                        price {
                            amount
                            currencyCode
                        }
                    }
                }
            }
        `;

        if (handles.length) {
            const productsSelection = handles
                .map((handle, index) => {
                    const escapedHandle = handle.replace(/"/g, '\\"');
                    return `product${index}: productByHandle(handle: "${escapedHandle}") { ${fragment} }`;
                })
                .join('\n');

            return `query StorefrontProductsByHandle {\n${productsSelection}\n}`;
        }

        if (collectionHandle) {
            const escapedHandle = collectionHandle.replace(/"/g, '\\"');
            return `query StorefrontCollectionProducts {\n  collection: collectionByHandle(handle: "${escapedHandle}") {\n    products(first: ${limit}) {\n      edges {\n        node {\n          ${fragment}\n        }\n      }\n    }\n  }\n}`;
        }

        return `query StorefrontLatestProducts {\n  products(first: ${limit}, sortKey: CREATED_AT, reverse: true) {\n    edges {\n      node {\n        ${fragment}\n      }\n    }\n  }\n}`;
    };

    const fetchStorefrontProducts = async (domain, options) => {
        const { token, apiVersion, handles, collectionHandle, limit } = options;
        if (!token) {
            return [];
        }

        const endpoint = `https://${domain}/api/${apiVersion}/graphql.json`;
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Shopify-Storefront-Access-Token': token
            },
            body: JSON.stringify({
                query: buildStorefrontQuery({ handles, collectionHandle, limit })
            })
        });

        if (!response.ok) {
            const error = new Error(`Shopify Storefront request failed with status ${response.status}`);
            error.status = response.status;
            throw error;
        }

        const payload = await response.json();

        if (payload?.errors) {
            const error = new Error('Shopify Storefront responded with errors');
            error.details = payload.errors;
            throw error;
        }

        const data = payload?.data || {};
        let products = [];

        if (handles.length) {
            products = Object.keys(data)
                .map(key => normalizeStorefrontProduct(data[key]))
                .filter(Boolean);
        } else if (collectionHandle) {
            products = Array.isArray(data?.collection?.products?.edges)
                ? data.collection.products.edges
                      .map(edge => normalizeStorefrontProduct(edge?.node))
                      .filter(Boolean)
                : [];
        } else if (Array.isArray(data?.products?.edges)) {
            products = data.products.edges
                .map(edge => normalizeStorefrontProduct(edge?.node))
                .filter(Boolean);
        }

        return products;
    };

    containers.forEach(container => {
        const grid = container.querySelector('.shopify-products__grid');
        const errorMessage = container.querySelector('.shopify-products__error');

        if (!grid) {
            return;
        }

        [
            ['shopifyGridTemplate', '--shopify-grid-template'],
            ['shopifyGridColumns', '--shopify-grid-template'],
            ['shopifyGridMin', '--shopify-grid-min'],
            ['shopifyGridGap', '--shopify-grid-gap'],
            ['shopifyGridFlow', '--shopify-grid-flow'],
            ['shopifyGridAutoRows', '--shopify-grid-auto-rows'],
            ['shopifyGridRows', '--shopify-grid-auto-rows'],
            ['shopifyGridJustify', '--shopify-grid-justify'],
            ['shopifyGridAlign', '--shopify-grid-align']
        ].forEach(([dataKey, cssVar]) => {
            const value = container.dataset[dataKey];

            if (typeof value === 'string' && value.trim()) {
                grid.style.setProperty(cssVar, value.trim());
            }
        });

        const domain = sanitizeDomain(container.dataset.shopifyShop);
        const limit = Number.parseInt(container.dataset.shopifyLimit, 10) || 3;
        const locale = container.dataset.shopifyLocale || 'sv-SE';
        const currency = container.dataset.shopifyCurrency || 'SEK';
        const ctaLabel = container.dataset.shopifyCtaLabel || 'Visa produkt';
        const collectionHandle = container.dataset.shopifyCollection;
        const fallbackUrl = container.dataset.shopifyFallbackUrl || `https://${domain}`;
        const handles = (container.dataset.shopifyProductHandles || '')
            .split(',')
            .map(handle => handle.trim())
            .filter(Boolean);
        const storefrontToken = (container.dataset.shopifyStorefrontToken || '').trim();
        const storefrontApiVersion = (container.dataset.shopifyStorefrontApiVersion || '2023-10').trim();

        const canUseStorefront = Boolean(storefrontToken);

        const loadFromStorefront = async () =>
            fetchStorefrontProducts(domain, {
                token: storefrontToken,
                apiVersion: storefrontApiVersion || '2023-10',
                handles,
                collectionHandle,
                limit
            });

        const showError = () => {
            if (errorMessage) {
                errorMessage.hidden = false;
            }

            grid.innerHTML = '';

            const fallbackCard = document.createElement('article');
            fallbackCard.className = 'card shopify-product shopify-product--empty';
            const body = document.createElement('div');
            body.className = 'shopify-product__content';
            const heading = document.createElement('h3');
            heading.className = 'shopify-product__title';
            heading.textContent = container.dataset.shopifyEmptyTitle || 'Produkterna kunde inte hämtas just nu.';
            const action = document.createElement('a');
            action.href = fallbackUrl;
            action.target = '_blank';
            action.rel = 'noopener';
            action.className = 'shopify-product__cta';
            action.textContent = container.dataset.shopifyEmptyCta || 'Till butiken';

            body.appendChild(heading);
            body.appendChild(action);
            fallbackCard.appendChild(body);
            grid.appendChild(fallbackCard);
            applyReveal([fallbackCard]);
        };

        const renderProducts = products => {
            if (!Array.isArray(products) || !products.length) {
                showError();
                return;
            }

            grid.innerHTML = '';
            const cards = products.slice(0, limit).map(product =>
                buildProductCard(domain, product, { currency, locale, ctaLabel })
            );

            cards.forEach(card => {
                grid.appendChild(card);
            });

            applyReveal(cards);
        };

        const loadProducts = async () => {
            try {
                if (handles.length) {
                    const results = await Promise.all(
                        handles.map(async handle => {
                            try {
                                const data = await fetchJson(`https://${domain}/products/${handle}.json`);
                                return data && data.product ? data.product : null;
                            } catch (error) {
                                return null;
                            }
                        })
                    );

                    const products = results.filter(Boolean);

                    if (!products.length && canUseStorefront) {
                        const storefrontProducts = await loadFromStorefront();
                        renderProducts(storefrontProducts);
                        return;
                    }

                    renderProducts(products);
                    return;
                }

                let endpoint = `https://${domain}/products.json?limit=${limit}`;
                if (collectionHandle) {
                    endpoint = `https://${domain}/collections/${collectionHandle}/products.json?limit=${limit}`;
                }

                const data = await fetchJson(endpoint);
                const products = Array.isArray(data?.products) ? data.products : [];

                if (!products.length && canUseStorefront) {
                    const storefrontProducts = await loadFromStorefront();
                    renderProducts(storefrontProducts);
                    return;
                }

                renderProducts(products);
            } catch (error) {
                if (canUseStorefront && (error?.status === 401 || error?.status === 403)) {
                    try {
                        const storefrontProducts = await loadFromStorefront();
                        renderProducts(storefrontProducts);
                        return;
                    } catch (storefrontError) {
                        // fall through to showError below
                    }
                }

                showError();
            }
        };

        if (errorMessage) {
            errorMessage.hidden = true;
        }

        loadProducts();
    });
})();
