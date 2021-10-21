<template>
    <div>
        <div class="mb-2">
            <p class="text-muted">
                Syncing the subscribers will validate the subscriptions of each name, if invalid their entry will be invalidated and not listed in any whitelist links.<br>
                This action can take some time depending on the list size.
            </p>
            <span>Whitelist info:</span>
            <ul>
                <li>
                    Total amount on whitelist: <span class="font-weight-bold">{{ infoData.total }}</span>
                </li>
                <li>
                    Total amount of subscriber names: <span class="font-weight-bold">{{ infoData.subscribers }}</span>
                </li>
                <li>
                    Total amount of custom names: <span class="font-weight-bold">{{ infoData.custom }}</span>
                </li>
                <li>
                    Total amount of invalid subscriptions: <span class="font-weight-bold">{{ infoData.invalid }}</span>
                </li>
                <li>
                    Total amount of valid Minecraft names: <span class="font-weight-bold">{{ infoData.minecraft }}</span>
                </li>
                <li>
                    Total amount of linked Steam IDs: <span class="font-weight-bold">{{ infoData.steam }}</span>
                </li>
            </ul>
            <b-button variant="primary" class="mt-1" @click="updateList">
                <fa icon="sync"></fa> Refresh List
            </b-button>
            <b-button :variant="timerToggle ? 'primary': 'outline-primary'" class="mt-1" :pressed.sync="timerToggle" @click="toggleUpdateInterval">
                <fa icon="sync" :spin="timerToggle"></fa> Auto Refresh List
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
                  :api-url="route('broadcaster.data')"
                  :css="css.table"
                  :sort-order="sortOrder"
                  :show-sort-icons="true"
                  :per-page="perPage"
                  :row-class="onRowClass"
                  :append-params="moreParams"
                  pagination-path=""
                  track-by="hash_id"
                  @vuetable:pagination-data="onPageinationData"
                  @vuetable:load-success="onRefresh"
        >
            <template v-slot:actions="props">
                <div slot="actions">
                    <button class="btn btn-danger mt-1"
                            @click="onDeleteItem(props.rowData.hash_id)"
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
import Vuetable from 'vuetable-3/src/components/Vuetable'
import VuetablePagination from 'vuetable-3/src/components/VuetablePagination'
import VuetablePaginationInfo from 'vuetable-3/src/components/VuetablePaginationInfo'
import BootstrapCss from './bootstrap'
import SearchBar from './SearchBarComponent'
import ky from 'ky'

const token = $('meta[name="csrf-token"]').attr('content');
const api = ky.extend({
    hooks: {
        beforeRequest: [
            request => {
                request.headers.set('X-Requested-With', 'XMLHttpRequest');
                request.headers.set('X-CSRF-TOKEN', token);
            }
        ]
    }
});


Vue.use(Vuetable);
export default {
    components: {
        Vuetable,
        VuetablePagination,
        VuetablePaginationInfo,
        SearchBar
    },
    props: {
        channel: {
            type: String
        }
    },
    data() {
        return {
            timerToggle: true,
            timer: '',
            perPage: 15,
            fields: [
                {
                    name: 'username',
                    sortField: 'username'

                },
                {
                    name: 'status',
                    title: 'Status',
                    formatter: (val) => {
                        let status = val.valid ?
                            '<i class="fas fa-check text-success" data-toggle="tooltip" data-placement="top" data-tippy-content="Valid subscription"></i>':
                            '<i class="fas fa-times text-danger" data-toggle="tooltip" data-placement="top" data-tippy-content="Invalid subscription"></i>';
                        if (val.minecraft !== "") {
                            status += ' <img class="minecraft_logo" src="/images/minecraft_logo_success.png" data-toggle="tooltip" data-placement="top" data-tippy-content="Minecraft name: ' + val.minecraft + '">';
                        }

                        if (val.steam) {
                            status += ' <i class="fab fa-steam text-success" data-toggle="tooltip" data-placement="top" data-tippy-content="Steam Linked">';
                        }

                        return status;
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
    created() {
        this.enableUpdateInterval();
        Echo.private(`channel.${this.channel}`)
            .notification((notification) => {
                if (notification.type === "App\\Notifications\\SubSyncDone") {
                    this.updateList(false);
                    this.$bvToast.toast("Userlist subscriptions synced", {
                        title: 'Subscriber Whitelist',
                        variant: 'success',
                        solid: true,
                        autoHideDelay: 2000
                    });
                } else if (notification.type === "App\\Notifications\\MCSyncDone") {
                    this.updateList(false);
                }
                refreshTooltips();
            });
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
            return !dataItem.status.valid ? 'table-danger': ''
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
        toggleUpdateInterval() {
            if (this.timerToggle) {
                this.enableUpdateInterval();
            } else {
                this.disableUpdateInterval();
            }
        },
        disableUpdateInterval() {
            clearInterval(this.timer);
            this.timer = '';
        },
        enableUpdateInterval() {
            this.timer = setInterval(this.updateList, 30000);
        },
        updateList: _.debounce(function (toast = true) {
            this.$refs.vuetable.refresh();
            if (toast) {
                this.$bvToast.toast("Userlist refreshed", {
                    title: 'Subscriber Whitelist',
                    variant: 'success',
                    solid: true,
                    autoHideDelay: 1000
                });
            }
        }, 1000, { leading: true, trailing: false}),
        sync: _.debounce(function () {
            api.post(this.route('broadcaster.sync')).then(() => {
                this.$bvToast.toast("Queued userlist syncing", {
                    title: 'Subscriber Whitelist',
                    variant: 'primary',
                    solid: true,
                    autoHideDelay: 2000
                });
            });
        }, 3000, { leading: true, trailing: false}),
        removeInvalid() {
            api.delete(this.route('broadcaster.invalid')).then(async response => {
                if (response.ok) {
                    this.$refs.vuetable.refresh();
                    this.$refs['remove-invalid'].hide();
                    this.$bvToast.toast("Successfully removed invalid subscriptions", {
                        title: 'Subscriber Whitelist',
                        variant: 'success',
                        solid: true,
                        autoHideDelay: 2000
                    });
                } else {
                    let error = await response.json();
                    this.$bvToast.toast(error.message, {
                        title: 'Subscriber Whitelist',
                        variant: 'danger',
                        solid: true,
                        autoHideDelay: 2000
                    });
                }
            });
        },
        removeAll() {
            api.delete(this.route('broadcaster.delete'), {throwHttpErrors: false}).then(async response => {
                if (response.ok) {
                    this.$refs.vuetable.refresh();
                    this.$refs['remove-all'].hide();
                    this.$bvToast.toast("Successfully removed all usernames", {
                        title: 'Subscriber Whitelist',
                        variant: 'success',
                        solid: true,
                        autoHideDelay: 2000
                    });
                } else {
                    let error = await response.json();
                    this.$bvToast.toast(error.message, {
                        title: 'Subscriber Whitelist',
                        variant: 'danger',
                        solid: true,
                        autoHideDelay: 2000
                    });
                }
            });
        },
        onDeleteItem(id) {
            api.delete(this.route('broadcaster.delete_entry', {id: id}), {throwHttpErrors: false}).then(async response => {
                if (response.ok) {
                    let data = await response.json();
                    this.$refs.vuetable.refresh();
                    this.$refs['remove-all'].hide();
                    this.$bvToast.toast("Successfully removed " + data.user, {
                        title: 'Subscriber Whitelist',
                        variant: 'success',
                        solid: true,
                        autoHideDelay: 2000
                    });
                } else {
                    let error = await response.json();
                    this.$bvToast.toast(error.message, {
                        title: 'Subscriber Whitelist',
                        variant: 'danger',
                        solid: true,
                        autoHideDelay: 2000
                    });
                }
            });
        },
        onRefresh() {
            api.get(this.route('broadcaster.list_stats')).then(async response => {
                this.infoData = await response.json();
            });
        }
    },
    beforeDestroy() {
        this.disableUpdateInterval();
    },
    tableClass: 'table table-striped table-bordered',
    ascendingIcon: 'glyphicon glyphicon-chevron-up',
    descendingIcon: 'glyphicon glyphicon-chevron-down',
    handleIcon: 'glyphicon glyphicon-menu-hamburger'
}
</script>
