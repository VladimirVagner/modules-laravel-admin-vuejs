import Vue from 'vue';
import Admin from './Admin';
import vuetify from './plugins/vuetify'

new Vue({
    vuetify,
    render: h => h(Admin)
}).$mount('#admin');
