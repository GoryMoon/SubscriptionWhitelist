export default {
    table: {
        tableClass: 'table table-striped table-bordered table-hover table-responsive-sm',
        loadingClass: 'loading',
        sortableIcon: 'fas fa-sort',
        ascendingIcon: 'fas fa-sort-up',
        descendingIcon: 'fas fa-sort-down',
        handleIcon: 'fas fa-bars text-secondary',

        tableWrapper: '',
        tableHeaderClass: 'mb-0',
        tableBodyClass: 'mb-0',
        ascendingClass: 'sorted-asc',
        descendingClass: 'sorted-desc',
        detailRowClass: 'vuetable-detail-row',
        renderIcon(classes, options) {
            return `<i class="${classes.join(' ')}"></i>`
        }
    },

    pagination: {
        wrapperClass: 'pagination mt-2 float-left',
        activeClass: 'active',
        disabledClass: 'disabled',
        pageClass: 'cursor page-item page-link',
        linkClass: 'cursor page-item page-link',
        paginationClass: 'pagination',
        paginationInfoClass: 'mt-2 text-muted',
        dropdownClass: 'form-control',
        icons: {
            first: 'fas fa-angle-double-left',
            prev: 'fas fa-angle-left',
            next: 'fas fa-angle-right',
            last: 'fas fa-angle-double-right',
        },
    },
    paginationInfo: {
        infoClass: 'mt-2 text-muted',
    }
}