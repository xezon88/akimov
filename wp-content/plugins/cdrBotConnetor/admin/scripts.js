class cdrConnectorPlugin {
  constructor () {
    this.body = document.querySelector('#wpbody-content')
    console.log('HELLO FROM CONNECTOR')
  }

  async getCards () {
    let res, codeBlock

    codeBlock = this.createEl('pre')

    res = await this.sendAJAX('saveCard', 'hello maaaan')
    res = res.slice(0, -1)

    this.appendNodes(codeBlock, [res])
    this.body.append(codeBlock)
    console.log(codeBlock);
  }

  async sendAJAX (action, data) {
    let body, response, responseHandler

    body = {action, data}
    responseHandler = res => console.log(res)

    await jQuery.post(ajaxurl, body, res => response = res)
    return response 
  }

  createEl (tag, classes = [], innerText = '') {
    let node = document.createElement(tag)
  
    if (innerText != '') {node.innerText = innerText}
    if (classes.length > 0) {
      classes.forEach(item => node.classList.add(item))
    }
  
    return node
  }

  appendNodes(parent, childrens) {
    childrens.forEach(node => parent.append(node))
  }
}


window.addEventListener('load', () => {
  let connector = new cdrConnectorPlugin()
  connector.getCards()
})