<script setup lang="ts">
// No imports needed - using static asset paths
</script>

<template>
    <!-- Site Header - Exact structure from ECL starter template -->
    <header class="ecl-site-header ecl-site-header-with-logo-l ecl-site-header--has-menu" data-ecl-auto-init="SiteHeader">
        <div class="ecl-site-header__inner">
            <div class="ecl-site-header__background">
                <div class="ecl-site-header__header">
                    <div class="ecl-site-header__container ecl-container">
                        <div class="ecl-site-header__top" data-ecl-site-header-top>
                            <a href="/" class="ecl-link ecl-link--standalone ecl-site-header__logo-link">
                                <img class="ecl-site-header__logo-image ecl-site-header__logo-image--l" 
                                     src="/ecl-assets/images/logo/logo-ec--en.svg" 
                                     alt="European Commission" />
                            </a>
                            <div class="ecl-site-header__action">
                                <div class="ecl-site-header__language">
                                    <a class="ecl-button ecl-button--tertiary ecl-site-header__language-selector" 
                                       href="#" 
                                       data-ecl-language-selector 
                                       role="button" 
                                       aria-label="Change language, current language is English - EN" 
                                       aria-controls="language-list-overlay">
                                        <span class="ecl-site-header__language-icon">
                                            <svg class="ecl-icon ecl-icon--s ecl-site-header__icon" focusable="false" aria-hidden="false" role="img">
                                                <title>EN</title>
                                                <use href="/ecl-assets/icons/icons.svg#global"></use>
                                            </svg>
                                        </span>
                                        EN
                                    </a>
                                    <div class="ecl-site-header__language-container" 
                                         id="language-list-overlay" 
                                         hidden 
                                         data-ecl-language-list-overlay 
                                         aria-labelledby="ecl-site-header__language-title" 
                                         role="dialog">
                                        <div class="ecl-site-header__language-header">
                                            <div class="ecl-site-header__language-title" id="ecl-site-header__language-title">
                                                Select your language
                                            </div>
                                            <button class="ecl-button ecl-button--tertiary ecl-site-header__language-close ecl-button--icon-only" 
                                                    type="submit" 
                                                    data-ecl-language-list-close>
                                                <span class="ecl-button__container">
                                                    <span class="ecl-button__label" data-ecl-label="true">Close</span>
                                                    <svg class="ecl-icon ecl-icon--m ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                                                        <use href="/ecl-assets/icons/icons.svg#close"></use>
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="ecl-site-header__language-content" data-ecl-language-list-content>
                                            <div class="ecl-site-header__language-category" data-ecl-language-list-eu>
                                                <div class="ecl-site-header__language-category-title">Official EU languages:</div>
                                                <ul class="ecl-site-header__language-list">
                                                    <li class="ecl-site-header__language-item">
                                                        <a href="#" 
                                                           class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-site-header__language-link ecl-site-header__language-link--active" 
                                                           hreflang="en">
                                                            <span class="ecl-site-header__language-link-code">en</span>
                                                            <span class="ecl-site-header__language-link-label" lang="en">English</span>
                                                        </a>
                                                    </li>
                                                    <li class="ecl-site-header__language-item">
                                                        <a href="#" 
                                                           class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-site-header__language-link" 
                                                           hreflang="fr">
                                                            <span class="ecl-site-header__language-link-code">fr</span>
                                                            <span class="ecl-site-header__language-link-label" lang="fr">français</span>
                                                        </a>
                                                    </li>
                                                    <li class="ecl-site-header__language-item">
                                                        <a href="#" 
                                                           class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-site-header__language-link" 
                                                           hreflang="de">
                                                            <span class="ecl-site-header__language-link-code">de</span>
                                                            <span class="ecl-site-header__language-link-label" lang="de">Deutsch</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="ecl-site-header__search-container" role="search">
                                    <a class="ecl-button ecl-button--tertiary ecl-site-header__search-toggle" 
                                       href="#" 
                                       data-ecl-search-toggle="true" 
                                       aria-controls="search-form-id" 
                                       aria-expanded="false">
                                        <svg class="ecl-icon ecl-icon--s ecl-site-header__icon" focusable="false" aria-hidden="false" role="img">
                                            <title>Search</title>
                                            <use href="/ecl-assets/icons/icons.svg#search"></use>
                                        </svg>
                                        Search
                                    </a>
                                    <form class="ecl-search-form ecl-site-header__search" 
                                          role="search" 
                                          data-ecl-search-form 
                                          id="search-form-id">
                                        <div class="ecl-form-group">
                                            <label for="search-input-id" id="search-input-id-label" class="ecl-form-label ecl-search-form__label">Search</label>
                                            <input id="search-input-id" 
                                                   class="ecl-text-input ecl-text-input--m ecl-search-form__text-input" 
                                                   type="search" 
                                                   placeholder="Search..." />
                                        </div>
                                        <button class="ecl-button ecl-button--ghost ecl-search-form__button" type="submit">
                                            <span class="ecl-button__container">
                                                <svg class="ecl-icon ecl-icon--xs ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                                                    <use href="/ecl-assets/icons/icons.svg#search"></use>
                                                </svg>
                                                <span class="ecl-button__label" data-ecl-label="true">Search</span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ecl-site-header__banner">
            <div class="ecl-container">
                <div class="ecl-site-header__site-name">Sandbox ECL Demo</div>
            </div>
        </div>
        <nav class="ecl-menu" 
             data-ecl-menu 
             data-ecl-menu-max-lines="2" 
             data-ecl-auto-init="Menu" 
             data-ecl-menu-label-open="Menu" 
             data-ecl-menu-label-close="Close" 
             aria-expanded="false" 
             role="navigation" 
             aria-label="Main navigation">
            <div class="ecl-menu__overlay"></div>
            <div class="ecl-container ecl-menu__container">
                <button class="ecl-button ecl-button--tertiary ecl-menu__open ecl-button--icon-only" 
                        type="button" 
                        data-ecl-menu-open 
                        aria-expanded="false">
                    <span class="ecl-button__container">
                        <svg class="ecl-icon ecl-icon--m ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                            <use href="/ecl-assets/icons/icons.svg#hamburger"></use>
                        </svg>
                        <svg class="ecl-icon ecl-icon--m ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                            <use href="/ecl-assets/icons/icons.svg#close"></use>
                        </svg>
                        <span class="ecl-button__label" data-ecl-label="true">Menu</span>
                    </span>
                </button>
                <section class="ecl-menu__inner" data-ecl-menu-inner aria-label="Menu">
                    <header class="ecl-menu__inner-header">
                        <button class="ecl-button ecl-button--ghost ecl-menu__close ecl-button--icon-only" 
                                type="submit" 
                                data-ecl-menu-close>
                            <span class="ecl-button__container">
                                <span class="ecl-button__label" data-ecl-label="true">Close</span>
                                <svg class="ecl-icon ecl-icon--m ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                                    <use href="/ecl-assets/icons/icons.svg#close"></use>
                                </svg>
                            </span>
                        </button>
                        <div class="ecl-menu__title">Menu</div>
                    </header>
                    <ul class="ecl-menu__list" data-ecl-menu-list>
                        <li class="ecl-menu__item" data-ecl-menu-item>
                            <a href="/" class="ecl-link ecl-link--standalone ecl-menu__link" data-ecl-menu-link>Home</a>
                        </li>
                        <li class="ecl-menu__item" data-ecl-menu-item>
                            <a href="/ecl-demo" class="ecl-link ecl-link--standalone ecl-menu__link" data-ecl-menu-link>ECL Demo</a>
                        </li>
                    </ul>
                </section>
            </div>
        </nav>
    </header>
</template>
