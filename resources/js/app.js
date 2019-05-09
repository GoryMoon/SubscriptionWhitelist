/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

window.Vue = require('vue');

require('bootstrap4-toggle');
import { library } from '@fortawesome/fontawesome-svg-core';
import { faTwitch, faPaypal } from '@fortawesome/free-brands-svg-icons'
import {
    faPlus,
    faTrash,
    faTimes,
    faStar,
    faSave,
    faSearch,
    faUser,
    faPaperPlane,
    faSync,
    faSort,
    faSortUp,
    faSortDown,
    faAngleLeft,
    faAngleRight,
    faAngleDoubleLeft,
    faAngleDoubleRight,
    faEdit,
    faGripLinesVertical
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(
    faTwitch, faPlus, faTrash, faTimes, faStar, faSave, faSearch, faUser, faPaperPlane, faSync,
    faSort, faSortUp, faSortDown, faAngleLeft, faAngleRight, faAngleDoubleLeft, faAngleDoubleRight,
    faEdit, faPaypal, faGripLinesVertical
    );

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

import Raphael from 'raphael/raphael'
global.Raphael = Raphael

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('fa', FontAwesomeIcon);
Vue.component('add-user-component', require('./components/AddUserComponent.vue').default);
Vue.component('user-list-component', require('./components/UserListComponent').default);
Vue.component('remove-account-component', require('./components/RemoveAccountComponent').default);
Vue.component('sub-manage-component', require('./components/SubManageComponent').default);
Vue.component('request-chart-component', require('./components/RequestChartComponent').default);
Vue.component('show-email-component', require('./components/ShowEmailComponent').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const vm = new Vue({
    el: '#app'
});

$(document).ready(function () {
    $('.selectable').click(function () {
        $(this).select();
    });

    refreshTooltips();
});

window.refreshTooltips = function refreshTooltips() {
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
}