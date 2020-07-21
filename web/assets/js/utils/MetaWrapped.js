export default class {
  constructor (data, metadata = {}) {
    this.fill(data)
    this.fill(metadata)
  }

  fill (data) {
    for (const field in data) {
      this[field] = data[field]
    }
  }

  static wrap (newItems, oldItems, metadata = {}) {
    for (let i = 0; i < newItems.length; i++) {
      let wrapped = oldItems.find(x => x.id === newItems[i].id)
      if (wrapped) {
        wrapped.fill(newItems[i])
      } else {
        wrapped = new this(newItems[i], metadata)
      }

      newItems.splice(i, 1, wrapped)
    }

    return newItems
  }
}
