/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/Directive.vue
 */
export default function permission(modules, permission) {
    const moduleList = this.$page.props.modules;
    if(moduleList[modules] != undefined){
        let hasPermission = moduleList[modules].includes(permission);
        
        return hasPermission;
    }else{
        console.error(`Modules ${ modules } not active for this user!"`);
        return false;
    }
//   if (value) {
//     // const permissions = store.getters && store.getters.permissions;
//     // const requiredPermissions = value;

//     // const hasPermission = permissions.some(permission => {
//     //   return requiredPermissions.includes(permission);
//     // });

//     // return hasPermission;
//     console.log('wqe');
//   } else {
//     console.error(`Need permissions! Like v-permission="['manage permission','edit article']"`);
//     return false;
//   }
}