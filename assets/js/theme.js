(function () {
    const nav = document.querySelector('.primary-navigation');
    const toggle = document.querySelector('.menu-toggle');
    const navLabels = window.editorialStarterNav || {};
    const expandText = navLabels.expand || 'Expand submenu';
    const collapseText = navLabels.collapse || 'Collapse submenu';

    const trackEvent = (eventName, params = {}) => {
        if (Array.isArray(window.dataLayer)) {
            window.dataLayer.push({
                event: eventName,
                ...params
            });
        }

        if (typeof window.gtag === 'function') {
            window.gtag('event', eventName, params);
        }
    };

    const findDirectChild = (item, className) =>
        Array.from(item.children || []).find(
            child => child.classList && child.classList.contains(className)
        );

    const submenuItems = nav ? Array.from(nav.querySelectorAll('.menu-item-has-children')) : [];

    const resetSubmenuItem = item => {
        item.classList.remove('is-open');

        const toggleButton = findDirectChild(item, 'submenu-toggle');
        if (toggleButton) {
            toggleButton.setAttribute('aria-expanded', 'false');
            const srText = toggleButton.querySelector('.screen-reader-text');
            const label = toggleButton.dataset.label || '';
            if (srText) {
                srText.textContent = `${expandText} ${label}`.trim();
            }
        }
    };

    const closeSubmenuTree = item => {
        resetSubmenuItem(item);
        item.querySelectorAll('.menu-item.is-open').forEach(resetSubmenuItem);
    };

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));

            if (!isOpen) {
                submenuItems.forEach(closeSubmenuTree);
            }
        });
    }

    let submenuId = 0;

    submenuItems.forEach(item => {
        if (findDirectChild(item, 'submenu-toggle')) {
            return;
        }

        const trigger = item.querySelector('a');
        const submenu = findDirectChild(item, 'sub-menu');

        if (!trigger || !submenu) {
            return;
        }

        submenuId += 1;
        if (!submenu.id) {
            submenu.id = `primary-submenu-${submenuId}`;
        }

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'submenu-toggle';
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-haspopup', 'true');
        button.setAttribute('aria-controls', submenu.id);

        const label = trigger.textContent.trim();
        button.dataset.label = label;

        const icon = document.createElement('span');
        icon.className = 'submenu-toggle__icon';
        button.appendChild(icon);

        const srText = document.createElement('span');
        srText.className = 'screen-reader-text';
        srText.textContent = `${expandText} ${label}`.trim();
        button.appendChild(srText);

        trigger.after(button);

        button.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();

            const expanded = item.classList.toggle('is-open');
            button.setAttribute('aria-expanded', String(expanded));
            srText.textContent = `${expanded ? collapseText : expandText} ${label}`.trim();

            if (expanded && item.parentElement) {
                Array.from(item.parentElement.children).forEach(sibling => {
                    if (sibling !== item && sibling.classList && sibling.classList.contains('is-open')) {
                        closeSubmenuTree(sibling);
                    }
                });
            }

            if (!expanded) {
                closeSubmenuTree(item);
            }
        });
    });

    if (nav) {
        nav.addEventListener('keyup', event => {
            if (event.key !== 'Escape') {
                return;
            }

            const currentOpen = event.target.closest('.menu-item.is-open');
            if (currentOpen) {
                closeSubmenuTree(currentOpen);
                const currentToggle = findDirectChild(currentOpen, 'submenu-toggle');
                if (currentToggle) {
                    currentToggle.focus();
                    return;
                }

                const parentMenuItem = currentOpen.parentElement && currentOpen.parentElement.closest('.menu-item');
                if (parentMenuItem) {
                    const parentToggle = findDirectChild(parentMenuItem, 'submenu-toggle');
                    if (parentToggle) {
                        parentToggle.focus();
                    }
                }
            }
        });
    }

    const toEventName = ctaType =>
        `${String(ctaType || '')
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '')}_cta_click`;

    document.querySelectorAll('[data-cta-type]').forEach(link => {
        link.addEventListener('click', () => {
            const ctaType = link.dataset.ctaType || '';
            const placement = link.dataset.ctaPlacement || 'unknown';

            if (!ctaType) {
                return;
            }

            trackEvent(toEventName(ctaType), { placement });
        });
    });

    document.querySelectorAll('.wp-block-jetpack-subscriptions form').forEach(form => {
        form.addEventListener('submit', () => {
            trackEvent('newsletter_submit', {
                placement: form.closest('.newsletter') ? 'newsletter_inline' : 'unknown'
            });
        });
    });

    document.querySelectorAll('.wp-block-jetpack-subscriptions button, .wp-block-jetpack-subscriptions input[type="submit"]').forEach(button => {
        button.addEventListener('click', () => {
            trackEvent('newsletter_cta_click', {
                placement: button.closest('.newsletter') ? 'newsletter_inline' : 'unknown'
            });
        });
    });

    let hasTrackedSubscribeSuccess = false;
    const successObserver = new MutationObserver(() => {
        if (hasTrackedSubscribeSuccess) {
            return;
        }

        const successMessage = document.querySelector('.wp-block-jetpack-subscriptions .success, .wp-block-jetpack-subscriptions .wp-block-jetpack-subscriptions__success');
        if (successMessage) {
            hasTrackedSubscribeSuccess = true;
            trackEvent('newsletter_success', {
                placement: successMessage.closest('.newsletter') ? 'newsletter_inline' : 'unknown'
            });
        }
    });

    successObserver.observe(document.body, {
        subtree: true,
        childList: true,
        attributes: true,
        attributeFilter: ['class', 'hidden', 'style']
    });

    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

        if (!reduceMotionQuery.matches) {
            heroSection.classList.add('is-booting');

            const activateHero = () => {
                if (heroSection.classList.contains('is-activated')) {
                    return;
                }

                requestAnimationFrame(() => {
                    heroSection.classList.add('is-activated');
                    heroSection.classList.remove('is-booting');
                });
            };

            if (document.readyState === 'complete') {
                setTimeout(activateHero, 160);
            } else {
                window.addEventListener('load', () => {
                    setTimeout(activateHero, 160);
                });
            }
        }
    }

    const heroCanvas = document.querySelector('.hero__stars');
    if (heroCanvas) {
        const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

        if (!reduceMotionQuery.matches) {
            const ctx = heroCanvas.getContext('2d');
            let width = 0;
            let height = 0;
            let frameId = null;
            let isVisible = !document.hidden;
            let inViewport = true;

            const stars = Array.from({ length: 120 }, () => ({
                x: Math.random(),
                y: Math.random(),
                z: Math.random()
            }));

            const resize = () => {
                width = heroCanvas.clientWidth;
                height = heroCanvas.clientHeight;
                const ratio = window.devicePixelRatio || 1;
                heroCanvas.width = width * ratio;
                heroCanvas.height = height * ratio;
                ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
            };

            const stopRender = () => {
                if (frameId) {
                    cancelAnimationFrame(frameId);
                    frameId = null;
                }
            };

            const render = () => {
                if (!isVisible || !inViewport) {
                    stopRender();
                    return;
                }

                ctx.clearRect(0, 0, width, height);
                ctx.fillStyle = 'rgba(208, 145, 93, 0.8)';

                stars.forEach(star => {
                    const x = (star.x - 0.5) * width * 1.2 + width / 2;
                    const y = (star.y - 0.5) * height * 1.2 + height / 2;
                    const radius = (1 - star.z) * 2 + 0.2;

                    ctx.beginPath();
                    ctx.arc(x, y, radius, 0, Math.PI * 2);
                    ctx.globalAlpha = 0.4 + (1 - star.z) * 0.6;
                    ctx.fill();
                    ctx.globalAlpha = 1;

                    star.z -= 0.0015;
                    if (star.z < 0.05) {
                        star.x = Math.random();
                        star.y = Math.random();
                        star.z = 1;
                    }
                });

                frameId = requestAnimationFrame(render);
            };

            const startRender = () => {
                if (frameId || !isVisible || !inViewport) {
                    return;
                }

                frameId = requestAnimationFrame(render);
            };

            resize();
            startRender();

            window.addEventListener('resize', resize);

            document.addEventListener('visibilitychange', () => {
                isVisible = !document.hidden;
                if (isVisible) {
                    startRender();
                } else {
                    stopRender();
                }
            });

            if ('IntersectionObserver' in window) {
                const viewportObserver = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        inViewport = entry.isIntersecting;

                        if (inViewport) {
                            startRender();
                        } else {
                            stopRender();
                        }
                    });
                });

                viewportObserver.observe(heroCanvas);
            }
        }
    }

    const revealSelector = '.card, .post-card, .status-card';

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.25
            }
        );

        window.editorialStarterApplyReveal = elements => {
            if (!elements) {
                return;
            }

            const items = Array.from(elements);
            items.forEach(item => {
                item.classList.add('is-reveal');
                observer.observe(item);
            });
        };

        window.editorialStarterApplyReveal(document.querySelectorAll(revealSelector));
    } else {
        window.editorialStarterApplyReveal = elements => {
            if (!elements) {
                return;
            }

            const items = Array.from(elements);
            items.forEach(item => {
                item.classList.add('is-visible');
            });
        };

        window.editorialStarterApplyReveal(document.querySelectorAll(revealSelector));
    }
})();
