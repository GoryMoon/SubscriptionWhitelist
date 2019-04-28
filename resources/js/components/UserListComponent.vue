<template>
    <div>
        <div class="mb-2">
            <p class="text-muted">
                Syncing the subscribers will validate the subscriptions of each name, if invalid their entry will<br>
                be invalidated and not listed in any whitelist links.<br>
                This action can take some time depending on the list size.
            </p>
            <span>Whitelist info:</span>
            <ul>
                <li>
                    Total amount on whitelists: {{ infoData.total }}
                </li>
                <li>
                    Total amount of subscribers: {{ infoData.subscribers }}
                </li>
                <li>
                    Total amount of custom names: {{ infoData.custom }}
                </li>
                <li>
                    Total amount of invalid subscriptions: {{ infoData.invalid }}
                </li>
            </ul>
            <b-button variant="primary" class="mt-1" @click="updateList">
                <fa icon="sync"></fa> Refresh List
            </b-button>
            <b-button variant="primary" class="mt-1" @click="sync">
                <fa icon="sync"></fa> Sync Subscriptions
            </b-button>
            <b-button variant="danger" class="mt-1" v-b-modal.remove-invalid>
                <fa icon="trash"></fa> Remove Invalid Subscriptions
            </b-button>
            <b-button variant="danger" class="mt-1" v-b-modal.remove-all>
                <fa icon="trash"></fa> Remove All
            </b-button>
        </div>
        <search-bar
                @reset="onFilterReset"
                v-on:set="onFilterSet"
        ></search-bar>
        <vuetable ref="vuetable"
                  :fields="fields"
                  :api-url="route + '/data'"
                  :css="css.table"
                  pagination-path=""
                  :sort-order="sortOrder"
                  :show-sort-icons="true"
                  :per-page="perPage"
                  :row-class="onRowClass"
                  :append-params="moreParams"
                  @vuetable:pagination-data="onPageinationData"
                  @vuetable:load-success="onRefresh"
        >
            <template v-slot:actions="props">
                <div slot="actions">
                    <button class="btn btn-danger mt-1"
                            @click="onDeleteItem(props.rowData.id)"
                            v-b-tooltip.hover
                            title="Remove"
                    ><fa icon="trash"></fa></button>
                </div>
            </template>
        </vuetable>
        <vuetable-pagination-info ref="paginationInfo"
                                  :css="css.paginationInfo"
                                  info-template="Displaying {from} to {to} of {total} users"
        ></vuetable-pagination-info>
        <div>
            <div class="mt-2 mr-2 float-left">
                <label for="per-page-select" class="sr-only">Rows to show</label>
                <select v-model="perPage" id="per-page-select" class="custom-select">
                    <option selected value="15">15</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <small class="form-text text-muted">Rows to show</small>
            </div>
            <vuetable-pagination ref="pagination"
                                 @vuetable-pagination:change-page="onChangePage"
                                 :css="css.pagination"
            ></vuetable-pagination>
        </div>
        <b-modal
                ref="remove-invalid"
                :lazy="true"
                id="remove-invalid"
                title="Remove Invalid Subscriptions"
        >
            <b-alert show variant="danger">
                <strong>Are you sure you want to remove all usernames with invalid subscriptions?</strong><br>
                They don't show in the list and the users would need to reenter their usernames to show on the list if they subscribe again.
            </b-alert>
            <div slot="modal-footer" class="w-100">
                <b-button
                        variant="primary"
                        size="md"
                        class="float-right"
                        @click="$bvModal.hide('remove-invalid')"
                >
                    Close
                </b-button>
                <b-button
                        variant="danger"
                        size="md"
                        class="float-right mr-1"
                        @click="removeInvalid"
                >
                    <fa icon="trash"></fa> Remove Invalid Subscriptions
                </b-button>
            </div>
        </b-modal>
        <b-modal
                ref="remove-all"
                :lazy="true"
                id="remove-all"
                title="Remove All"
        >
            <b-alert show variant="danger">
                <strong>Are you sure you want to remove all usernames?</strong><br>
                This will empty your whitelist from any name.<br>
                Your subscribers will need to reenter their usernames.
            </b-alert>
            <div slot="modal-footer" class="w-100">
                <b-button
                        variant="primary"
                        size="md"
                        class="float-right"
                        @click="$bvModal.hide('remove-all')"
                >
                    Close
                </b-button>
                <b-button
                        variant="danger"
                        size="md"
                        class="float-right mr-1"
                        @click="this.removeAll"
                >
                    <fa icon="trash"></fa> Remove All
                </b-button>
            </div>
        </b-modal>
    </div>
</template>
<script>
    import Vuetable from 'vuetable-2/src/components/Vuetable'
    import VuetablePagination from 'vuetable-2/src/components/VuetablePagination'
    import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo'
    import BootstrapCss from './bootstrap'
    import SearchBar from './SearchBarComponent'

    Vue.use(Vuetable);
export default {
    components: {
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
        SearchBar
    },
    props: {
        route: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            perPage: 15,
            fields: [
                {
                    name: 'username',
                    sortField: 'username'

                },
                {
                    name: 'is_valid',
                    title: 'Status',
                    formatter: (val) => {
                        return val ?
                            '<i class="fas fa-check text-success" data-toggle="tooltip" data-placement="top" title="Valid subscription"></i>':
                            '<i class="fas fa-times text-danger" data-toggle="tooltip" data-placement="top" title="Invalid subscription"></i>'
                    },
                    width: '90px',
                    sortField: 'valid'
                },
                {
                    name: 'is_subscriber',
                    title: 'Type',
                    formatter: (val) => {
                        return val ? 'Subscriber': 'Custom'
                    },
                    sortField: 'user_id'
                },
                {
                    name: 'actions',
                    width: '60px'
                }
            ],
            css: BootstrapCss,
            sortOrder: [
                {
                    field: 'id',
                    sortField: 'id',
                    direction: 'asc'
                }
            ],
            moreParams: {},
            infoData: {
                total: 0,
                subscribers: 0,
                custom: 0,
                invalid: 0
            }
        }
    },
    methods: {
        onPageinationData(data) {
            this.$refs.pagination.setPaginationData(data);
            this.$refs.paginationInfo.setPaginationData(data);
            window.refreshTooltips();
        },
        onChangePage(page) {
            this.$refs.vuetable.changePage(page);
        },
        onRowClass(dataItem, index) {
            return !dataItem.is_valid ? 'table-danger': ''
        },
        onFilterSet (filterText) {
            this.moreParams = {
                'filter': filterText
            };
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        onFilterReset () {
            this.moreParams = {};
            this.$nextTick(() => this.$refs.vuetable.refresh());
        },
        updateList: _.debounce(function () {
            this.$refs.vuetable.refresh();
            this.$bvToast.toast("Userlist refreshed", {
                title: 'Subscriber Whitelist',
                variant: 'success',
                solid: true
            });
        }, 1000, { leading: true, trailing: false}),
        sync: _.debounce(function () {
            axios.post(this.route + '/sync').then(() => {
                this.$bvToast.toast("Queued userlist syncing", {
                    title: 'Subscriber Whitelist',
                    variant: 'primary',
                    solid: true
                });
            });
        }, 3000, { leading: true, trailing: false}),
        removeInvalid() {
            axios.delete(this.route + '/invalid').then((response) => {
                this.$refs.vuetable.refresh();
                this.$refs['remove-invalid'].hide();
                this.$bvToast.toast("Successfully removed invalid subscriptions", {
                    title: 'Subscriber Whitelist',
                    variant: 'success',
                    solid: true
                });
            }).catch((error) => {
                this.$bvToast.toast(error, {
                    title: 'Subscriber Whitelist',
                    variant: 'danger',
                    solid: true
                });
            });
        },
        removeAll() {
            axios.delete(this.route + '/all').then(response => {
                this.$refs.vuetable.refresh();
                this.$refs['remove-all'].hide();
                this.$bvToast.toast("Successfully removed all usernames", {
                    title: 'Subscriber Whitelist',
                    variant: 'success',
                    solid: true
                });
            }).catch((error) => {
                this.$bvToast.toast(error, {
                    title: 'Subscriber Whitelist',
                    variant: 'danger',
                    solid: true
                });
            });
        },
        onDeleteItem(id) {
            axios.delete(this.route + '/' + id).then(response => {
                this.$refs.vuetable.refresh();
                this.$refs['remove-all'].hide();
                this.$bvToast.toast("Successfully removed " + response.data.user, {
                    title: 'Subscriber Whitelist',
                    variant: 'success',
                    solid: true
                });
            }).catch((error) => {
                let message = error.message;
                if (error.response) {
                    message = error.response.data;
                } else if (error.request) {
                    message = error.request;
                }
                this.$bvToast.toast(message, {
                    title: 'Subscriber Whitelist',
                    variant: 'danger',
                    solid: true
                });
            });
        },
        onRefresh() {
            axios.get(this.route + '/stats').then(response => {
                this.infoData = response.data;
            });
        }
    },
    tableClass: 'table table-striped table-bordered',
    ascendingIcon: 'glyphicon glyphicon-chevron-up',
    descendingIcon: 'glyphicon glyphicon-chevron-down',
    handleIcon: 'glyphicon glyphicon-menu-hamburger'
}
</script>