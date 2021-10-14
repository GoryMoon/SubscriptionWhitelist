/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import route from 'ziggyjs'; // dynamic dependency from composer package
import { Ziggy } from './ziggy';
window.Ziggy = Ziggy;

require('./bootstrap');
import Vue from 'vue';
window.Vue = Vue;
require('bootstrap4-toggle');
import {default as tippy} from 'tippy.js';
import 'tippy.js/dist/tippy.css';

import { library } from '@fortawesome/fontawesome-svg-core';
import { faTwitch, faCcPaypal, faGithub, faSteam, faPatreon } from '@fortawesome/free-brands-svg-icons';
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
    faGripLinesVertical,
    faList,
    faChartArea,
    faCopy,
    faUsers,
    faCheck,
    faCross,
    faLink,
    faExternalLinkAlt
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import BootstrapVue from 'bootstrap-vue';
import VueClipboard from 'vue-clipboard2';
import Raphael from 'raphael/raphael';

library.add(
    faTwitch, faPlus, faTrash, faTimes, faStar, faSave, faSearch, faUser, faPaperPlane, faSync,
    faSort, faSortUp, faSortDown, faAngleLeft, faAngleRight, faAngleDoubleLeft, faAngleDoubleRight,
    faEdit, faCcPaypal, faGripLinesVertical, faGithub, faList, faChartArea, faCopy, faUsers,
    faCheck, faCross, faSteam, faPatreon, faLink, faExternalLinkAlt
    );
Vue.component('fa', FontAwesomeIcon);
Vue.use(BootstrapVue);
Vue.use(VueClipboard);
Vue.mixin({
    methods: {
        route: route
    }
});
window.Raphael = Raphael;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

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
        for (let tooltip of $('[data-tippy-content]')) {
            if ("_tippy" in tooltip) {
                tooltip._tippy.destroy();
            }
        }
        tippy('[data-tippy-content]');
    });
};
