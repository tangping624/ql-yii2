/**
 * Created by kongy on 2015/8/5.
 *  带租户的cookie操作
 **/
define(function (require, exports, module) {
    require('./jquery.cookie');
    module.exports = {
        cookie: function (key, value, options) {
            if (key) {
                key = this.getTenantCodeKey(key);
            }

            return $.cookie(key, value, options);
        },

        removeCookie: function (key, options) {
            if (key) {
                key = this.getTenantCodeKey(key);
            }
            return $.removeCookie(key, options);
        },

        getTenantCodeKey: function (key) {
            var tenantCode = O.getToken();
            return tenantCode ? tenantCode + '_' + key : key;
        }
    }
});