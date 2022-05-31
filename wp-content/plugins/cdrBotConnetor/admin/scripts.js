class wpPluginAdmin {
  initPlugin () {
    console.log()
  }
}


window.addEventListener('load', () => {
  let pluginTemplate = new wpPluginTmplateAdmin()
  pluginTemplate.initPlugin()
})