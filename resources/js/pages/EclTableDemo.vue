<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EclLayout from '@/layouts/EclLayout.vue';
import { Link } from '@inertiajs/vue3';
import { home, eclDemo } from '@/routes';

defineOptions({
    layout: EclLayout,
});
</script>

<template>
    <Head title="ECL Table Demo - European Commission" />

    <!-- Page Header -->
    <div class="ecl-page-header">
        <div class="ecl-container">
            <nav class="ecl-page-header__breadcrumb ecl-breadcrumb" aria-label="You are here:" data-ecl-breadcrumb>
                <ol class="ecl-breadcrumb__container">
                    <li class="ecl-breadcrumb__segment" data-ecl-breadcrumb-item>
                        <Link :href="home()" class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-breadcrumb__link">Home</Link>
                        <svg class="ecl-icon ecl-icon--xs ecl-icon--rotate-90 ecl-breadcrumb__icon" 
                             focusable="false" 
                             aria-hidden="true">
                            <use href="/ecl-assets/icons/icons.svg#corner-arrow"></use>
                        </svg>
                    </li>
                    <li class="ecl-breadcrumb__segment" data-ecl-breadcrumb-item>
                        <Link :href="eclDemo()" class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-breadcrumb__link">ECL Demo</Link>
                        <svg class="ecl-icon ecl-icon--xs ecl-icon--rotate-90 ecl-breadcrumb__icon" 
                             focusable="false" 
                             aria-hidden="true">
                            <use href="/ecl-assets/icons/icons.svg#corner-arrow"></use>
                        </svg>
                    </li>
                    <li class="ecl-breadcrumb__segment ecl-breadcrumb__current-page" 
                        data-ecl-breadcrumb-item 
                        aria-current="page">
                        Table Demo
                    </li>
                </ol>
            </nav>
            <div class="ecl-page-header__body">
                <h1 class="ecl-page-header__title">ECL Table Components</h1>
                <p class="ecl-page-header__description">
                    Demonstration of various table styles and features from the EC Europa Component Library,
                    including default tables, zebra striping, multi-header layouts, and sortable columns.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="ecl-u-pv-xl">
        <div class="ecl-container">
            <!-- Introduction -->
            <p class="ecl-u-type-paragraph-lead">
                Tables are used to structure complex data in an organized way that is more user-friendly and discoverable. 
                The ECL provides several table variants to suit different data presentation needs.
            </p>

            <!-- Default Table -->
            <section class="ecl-u-mt-xl">
                <h2 class="ecl-u-type-heading-2">Default Table</h2>
                <p class="ecl-u-type-paragraph ecl-u-mb-m">
                    The standard table layout with clear headers and organized data cells. All tables are responsive 
                    and wrapped in <code>ecl-table-responsive</code> to ensure they work well on mobile devices.
                </p>
                
                <div class="ecl-table-responsive">
                    <table class="ecl-table" id="table-default">
                        <caption class="ecl-table__caption">European Commission Job Openings</caption>
                        <thead class="ecl-table__head">
                            <tr class="ecl-table__row">
                                <th id="default-header-1" scope="col" class="ecl-table__header">Job title</th>
                                <th id="default-header-2" scope="col" class="ecl-table__header">Grade</th>
                                <th id="default-header-3" scope="col" class="ecl-table__header">Location</th>
                                <th id="default-header-4" scope="col" class="ecl-table__header">Organization</th>
                                <th id="default-header-5" scope="col" class="ecl-table__header">Type of contract</th>
                            </tr>
                        </thead>
                        <tbody class="ecl-table__body">
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" class="ecl-table__cell">Administrators in Competition Law</td>
                                <td data-ecl-table-header="Grade" class="ecl-table__cell">AD7</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">
                                    Brussels (Belgium), Luxembourg (Luxembourg), Strasbourg (France)
                                </td>
                                <td data-ecl-table-header="Organization" class="ecl-table__cell">European Commission</td>
                                <td data-ecl-table-header="Type of contract" class="ecl-table__cell">Permanent official</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" class="ecl-table__cell">
                                    Administrators in Economic and Monetary Union Law
                                </td>
                                <td data-ecl-table-header="Grade" class="ecl-table__cell">AD7</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">
                                    Brussels (Belgium), Luxembourg (Luxembourg), Strasbourg (France)
                                </td>
                                <td data-ecl-table-header="Organization" class="ecl-table__cell">European Commission</td>
                                <td data-ecl-table-header="Type of contract" class="ecl-table__cell">Permanent official</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" class="ecl-table__cell">
                                    Administrators in Financial rules applicable to the EU budget
                                </td>
                                <td data-ecl-table-header="Grade" class="ecl-table__cell">AD7</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">
                                    Brussels (Belgium), Luxembourg (Luxembourg), Strasbourg (France)
                                </td>
                                <td data-ecl-table-header="Organization" class="ecl-table__cell">European Commission</td>
                                <td data-ecl-table-header="Type of contract" class="ecl-table__cell">Permanent official</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" class="ecl-table__cell">Corporate Support Officer</td>
                                <td data-ecl-table-header="Grade" class="ecl-table__cell">FG IV</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">Prague (Czech Republic)</td>
                                <td data-ecl-table-header="Organization" class="ecl-table__cell">European Commission</td>
                                <td data-ecl-table-header="Type of contract" class="ecl-table__cell">Permanent official</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Zebra Table -->
            <section class="ecl-u-mt-xl">
                <h2 class="ecl-u-type-heading-2">Zebra Striped Table</h2>
                <p class="ecl-u-type-paragraph ecl-u-mb-m">
                    Add the <code>ecl-table--zebra</code> modifier class to create alternating row colors, 
                    which helps improve readability for tables with many rows.
                </p>
                
                <div class="ecl-table-responsive">
                    <table class="ecl-table ecl-table--zebra" id="table-zebra">
                        <caption class="ecl-table__caption">EU Member States Population Data</caption>
                        <thead class="ecl-table__head">
                            <tr class="ecl-table__row">
                                <th id="zebra-header-1" scope="col" class="ecl-table__header">Country</th>
                                <th id="zebra-header-2" scope="col" class="ecl-table__header">Capital</th>
                                <th id="zebra-header-3" scope="col" class="ecl-table__header">Population (millions)</th>
                                <th id="zebra-header-4" scope="col" class="ecl-table__header">Area (km²)</th>
                                <th id="zebra-header-5" scope="col" class="ecl-table__header">Joined EU</th>
                            </tr>
                        </thead>
                        <tbody class="ecl-table__body">
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Country" class="ecl-table__cell">Germany</td>
                                <td data-ecl-table-header="Capital" class="ecl-table__cell">Berlin</td>
                                <td data-ecl-table-header="Population (millions)" class="ecl-table__cell">83.2</td>
                                <td data-ecl-table-header="Area (km²)" class="ecl-table__cell">357,022</td>
                                <td data-ecl-table-header="Joined EU" class="ecl-table__cell">1958</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Country" class="ecl-table__cell">France</td>
                                <td data-ecl-table-header="Capital" class="ecl-table__cell">Paris</td>
                                <td data-ecl-table-header="Population (millions)" class="ecl-table__cell">67.4</td>
                                <td data-ecl-table-header="Area (km²)" class="ecl-table__cell">643,801</td>
                                <td data-ecl-table-header="Joined EU" class="ecl-table__cell">1958</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Country" class="ecl-table__cell">Italy</td>
                                <td data-ecl-table-header="Capital" class="ecl-table__cell">Rome</td>
                                <td data-ecl-table-header="Population (millions)" class="ecl-table__cell">59.6</td>
                                <td data-ecl-table-header="Area (km²)" class="ecl-table__cell">301,340</td>
                                <td data-ecl-table-header="Joined EU" class="ecl-table__cell">1958</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Country" class="ecl-table__cell">Spain</td>
                                <td data-ecl-table-header="Capital" class="ecl-table__cell">Madrid</td>
                                <td data-ecl-table-header="Population (millions)" class="ecl-table__cell">47.4</td>
                                <td data-ecl-table-header="Area (km²)" class="ecl-table__cell">505,990</td>
                                <td data-ecl-table-header="Joined EU" class="ecl-table__cell">1986</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Country" class="ecl-table__cell">Poland</td>
                                <td data-ecl-table-header="Capital" class="ecl-table__cell">Warsaw</td>
                                <td data-ecl-table-header="Population (millions)" class="ecl-table__cell">38.0</td>
                                <td data-ecl-table-header="Area (km²)" class="ecl-table__cell">312,696</td>
                                <td data-ecl-table-header="Joined EU" class="ecl-table__cell">2004</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Country" class="ecl-table__cell">Netherlands</td>
                                <td data-ecl-table-header="Capital" class="ecl-table__cell">Amsterdam</td>
                                <td data-ecl-table-header="Population (millions)" class="ecl-table__cell">17.5</td>
                                <td data-ecl-table-header="Area (km²)" class="ecl-table__cell">41,543</td>
                                <td data-ecl-table-header="Joined EU" class="ecl-table__cell">1958</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Multi-Header Table -->
            <section class="ecl-u-mt-xl">
                <h2 class="ecl-u-type-heading-2">Multi-Header Table</h2>
                <p class="ecl-u-type-paragraph ecl-u-mb-m">
                    Complex tables can have multiple header rows using <code>rowspan</code> and <code>colspan</code> 
                    attributes to group related columns together.
                </p>
                
                <div class="ecl-table-responsive">
                    <table class="ecl-table" id="table-multi">
                        <caption class="ecl-table__caption">Project Staff Overview</caption>
                        <thead class="ecl-table__head">
                            <tr class="ecl-table__row">
                                <th id="multi-header-1" rowspan="2" scope="col" class="ecl-table__header">Job title</th>
                                <th id="multi-header-2" rowspan="2" scope="col" class="ecl-table__header">Grade</th>
                                <th id="multi-header-3" colspan="2" scope="colgroup" class="ecl-table__header">Employment Details</th>
                                <th id="multi-header-4" rowspan="2" scope="col" class="ecl-table__header">Location</th>
                            </tr>
                            <tr class="ecl-table__row">
                                <th id="multi-header-5" headers="multi-header-3" scope="col" class="ecl-table__header">Organization</th>
                                <th id="multi-header-6" headers="multi-header-3" scope="col" class="ecl-table__header">
                                    Type of contract
                                </th>
                            </tr>
                        </thead>
                        <tbody class="ecl-table__body">
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" headers="multi-header-1" class="ecl-table__cell">
                                    Senior Policy Advisor
                                </td>
                                <td data-ecl-table-header="Grade" headers="multi-header-2" class="ecl-table__cell">
                                    AD12
                                </td>
                                <td
                                    data-ecl-table-header="Organization"
                                    data-ecl-table-header-group="Employment Details"
                                    headers="multi-header-3 multi-header-5"
                                    class="ecl-table__cell ecl-table__cell--group">
                                    European Commission
                                </td>
                                <td
                                    data-ecl-table-header="Type of contract"
                                    headers="multi-header-3 multi-header-6"
                                    class="ecl-table__cell ecl-table__cell--group">
                                    Permanent official
                                </td>
                                <td data-ecl-table-header="Location" headers="multi-header-4" class="ecl-table__cell">
                                    Brussels (Belgium)
                                </td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" headers="multi-header-1" class="ecl-table__cell">
                                    Data Protection Officer
                                </td>
                                <td data-ecl-table-header="Grade" headers="multi-header-2" class="ecl-table__cell">
                                    AD10
                                </td>
                                <td
                                    data-ecl-table-header="Organization"
                                    data-ecl-table-header-group="Employment Details"
                                    headers="multi-header-3 multi-header-5"
                                    class="ecl-table__cell ecl-table__cell--group">
                                    European Data Protection Supervisor
                                </td>
                                <td
                                    data-ecl-table-header="Type of contract"
                                    headers="multi-header-3 multi-header-6"
                                    class="ecl-table__cell ecl-table__cell--group">
                                    Temporary agent
                                </td>
                                <td data-ecl-table-header="Location" headers="multi-header-4" class="ecl-table__cell">
                                    Brussels (Belgium)
                                </td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Job title" headers="multi-header-1" class="ecl-table__cell">
                                    Research Coordinator
                                </td>
                                <td data-ecl-table-header="Grade" headers="multi-header-2" class="ecl-table__cell">
                                    AD8
                                </td>
                                <td
                                    data-ecl-table-header="Organization"
                                    data-ecl-table-header-group="Employment Details"
                                    headers="multi-header-3 multi-header-5"
                                    class="ecl-table__cell ecl-table__cell--group">
                                    Joint Research Centre
                                </td>
                                <td
                                    data-ecl-table-header="Type of contract"
                                    headers="multi-header-3 multi-header-6"
                                    class="ecl-table__cell ecl-table__cell--group">
                                    Contract agent
                                </td>
                                <td data-ecl-table-header="Location" headers="multi-header-4" class="ecl-table__cell">
                                    Ispra (Italy)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Sortable Table -->
            <section class="ecl-u-mt-xl">
                <h2 class="ecl-u-type-heading-2">Sortable Table</h2>
                <p class="ecl-u-type-paragraph ecl-u-mb-m">
                    Tables can be made sortable by adding <code>data-ecl-auto-init="Table"</code> to the table element 
                    and <code>data-ecl-table-sort-toggle</code> to the column headers. Click any header to sort the data.
                </p>
                
                <div class="ecl-table-responsive">
                    <table
                        class="ecl-table"
                        id="table-sort"
                        data-ecl-table
                        data-ecl-auto-init="Table"
                        data-ecl-table-sort-label="Sort table">
                        <caption class="ecl-table__caption">EU Budget Allocations 2024 (Click headers to sort)</caption>
                        <thead class="ecl-table__head">
                            <tr class="ecl-table__row">
                                <th id="sort-header-1" data-ecl-table-sort-toggle scope="col" class="ecl-table__header">
                                    Programme
                                </th>
                                <th id="sort-header-2" data-ecl-table-sort-toggle scope="col" class="ecl-table__header">
                                    Budget (€ millions)
                                </th>
                                <th id="sort-header-3" data-ecl-table-sort-toggle scope="col" class="ecl-table__header">
                                    Directorate
                                </th>
                                <th id="sort-header-4" data-ecl-table-sort-toggle scope="col" class="ecl-table__header">
                                    Priority Area
                                </th>
                                <th id="sort-header-5" data-ecl-table-sort-toggle scope="col" class="ecl-table__header">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="ecl-table__body">
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Programme" class="ecl-table__cell">Horizon Europe</td>
                                <td data-ecl-table-header="Budget (€ millions)" class="ecl-table__cell">95,500</td>
                                <td data-ecl-table-header="Directorate" class="ecl-table__cell">DG Research and Innovation</td>
                                <td data-ecl-table-header="Priority Area" class="ecl-table__cell">Research & Innovation</td>
                                <td data-ecl-table-header="Status" class="ecl-table__cell">Active</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Programme" class="ecl-table__cell">Erasmus+</td>
                                <td data-ecl-table-header="Budget (€ millions)" class="ecl-table__cell">26,200</td>
                                <td data-ecl-table-header="Directorate" class="ecl-table__cell">DG Education and Culture</td>
                                <td data-ecl-table-header="Priority Area" class="ecl-table__cell">Education & Culture</td>
                                <td data-ecl-table-header="Status" class="ecl-table__cell">Active</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Programme" class="ecl-table__cell">Digital Europe</td>
                                <td data-ecl-table-header="Budget (€ millions)" class="ecl-table__cell">7,500</td>
                                <td data-ecl-table-header="Directorate" class="ecl-table__cell">DG Communications Networks</td>
                                <td data-ecl-table-header="Priority Area" class="ecl-table__cell">Digital Transformation</td>
                                <td data-ecl-table-header="Status" class="ecl-table__cell">Active</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Programme" class="ecl-table__cell">LIFE Programme</td>
                                <td data-ecl-table-header="Budget (€ millions)" class="ecl-table__cell">5,400</td>
                                <td data-ecl-table-header="Directorate" class="ecl-table__cell">DG Environment</td>
                                <td data-ecl-table-header="Priority Area" class="ecl-table__cell">Environment & Climate</td>
                                <td data-ecl-table-header="Status" class="ecl-table__cell">Active</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Programme" class="ecl-table__cell">Creative Europe</td>
                                <td data-ecl-table-header="Budget (€ millions)" class="ecl-table__cell">2,400</td>
                                <td data-ecl-table-header="Directorate" class="ecl-table__cell">DG Education and Culture</td>
                                <td data-ecl-table-header="Priority Area" class="ecl-table__cell">Culture & Media</td>
                                <td data-ecl-table-header="Status" class="ecl-table__cell">Active</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Programme" class="ecl-table__cell">EU4Health</td>
                                <td data-ecl-table-header="Budget (€ millions)" class="ecl-table__cell">5,100</td>
                                <td data-ecl-table-header="Directorate" class="ecl-table__cell">DG Health and Food Safety</td>
                                <td data-ecl-table-header="Priority Area" class="ecl-table__cell">Public Health</td>
                                <td data-ecl-table-header="Status" class="ecl-table__cell">Active</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Compact Table Example -->
            <section class="ecl-u-mt-xl">
                <h2 class="ecl-u-type-heading-2">Compact Data Table</h2>
                <p class="ecl-u-type-paragraph ecl-u-mb-m">
                    A simpler table with fewer columns, demonstrating how tables adapt to different data structures.
                </p>
                
                <div class="ecl-table-responsive">
                    <table class="ecl-table ecl-table--zebra" id="table-compact">
                        <caption class="ecl-table__caption">Key EU Institutions</caption>
                        <thead class="ecl-table__head">
                            <tr class="ecl-table__row">
                                <th id="compact-header-1" scope="col" class="ecl-table__header">Institution</th>
                                <th id="compact-header-2" scope="col" class="ecl-table__header">Location</th>
                                <th id="compact-header-3" scope="col" class="ecl-table__header">Established</th>
                            </tr>
                        </thead>
                        <tbody class="ecl-table__body">
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Institution" class="ecl-table__cell">European Commission</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">Brussels, Belgium</td>
                                <td data-ecl-table-header="Established" class="ecl-table__cell">1958</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Institution" class="ecl-table__cell">European Parliament</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">Strasbourg, France</td>
                                <td data-ecl-table-header="Established" class="ecl-table__cell">1952</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Institution" class="ecl-table__cell">Council of the European Union</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">Brussels, Belgium</td>
                                <td data-ecl-table-header="Established" class="ecl-table__cell">1958</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Institution" class="ecl-table__cell">European Court of Justice</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">Luxembourg</td>
                                <td data-ecl-table-header="Established" class="ecl-table__cell">1952</td>
                            </tr>
                            <tr class="ecl-table__row">
                                <td data-ecl-table-header="Institution" class="ecl-table__cell">European Central Bank</td>
                                <td data-ecl-table-header="Location" class="ecl-table__cell">Frankfurt, Germany</td>
                                <td data-ecl-table-header="Established" class="ecl-table__cell">1998</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Navigation to other demos -->
            <section class="ecl-u-mt-xl">
                <div class="ecl-u-bg-blue-5 ecl-u-pa-m" style="border-left: 4px solid #004494;">
                    <p class="ecl-u-type-paragraph ecl-u-mb-s">
                        <strong>More ECL Examples:</strong>
                    </p>
                    <p class="ecl-u-type-paragraph">
                        Return to the main <Link :href="eclDemo()" class="ecl-link ecl-link--standalone">ECL Demo</Link> 
                        to see other components like accordions, breadcrumbs, and typography examples.
                    </p>
                </div>
            </section>

            <!-- Usage Notes -->
            <section class="ecl-u-mt-xl ecl-u-mb-xl">
                <h2 class="ecl-u-type-heading-2">Table Best Practices</h2>
                <div class="ecl-u-mt-m">
                    <h3 class="ecl-u-type-heading-3">Do's</h3>
                    <ul class="ecl-unordered-list">
                        <li class="ecl-unordered-list__item">Reduce the table width to fit the content</li>
                        <li class="ecl-unordered-list__item">Align content left to right within cells</li>
                        <li class="ecl-unordered-list__item">Use a dash (-) in null data cells</li>
                        <li class="ecl-unordered-list__item">Adjust the styling to match the surrounding content</li>
                        <li class="ecl-unordered-list__item">Always wrap tables in <code>ecl-table-responsive</code> for mobile support</li>
                        <li class="ecl-unordered-list__item">Include proper <code>data-ecl-table-header</code> attributes for mobile views</li>
                    </ul>

                    <h3 class="ecl-u-type-heading-3 ecl-u-mt-m">Don'ts</h3>
                    <ul class="ecl-unordered-list">
                        <li class="ecl-unordered-list__item">Don't use tables for layout purposes</li>
                        <li class="ecl-unordered-list__item">Avoid making tables wider than necessary</li>
                        <li class="ecl-unordered-list__item">Don't omit table captions - they help accessibility</li>
                        <li class="ecl-unordered-list__item">Avoid excessive use of colspan/rowspan as it can confuse screen readers</li>
                    </ul>
                </div>
            </section>
        </div>
    </main>
</template>
