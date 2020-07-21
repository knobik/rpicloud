export default class {
  constructor () {
    this.errors = {}
  }

  record (errors) {
    this.errors = errors
  }

  has (field) {
    return typeof this.errors[field] !== 'undefined'
  }

  get (field) {
    if (this.has(field)) {
      return this.errors[field][0]
    }
  }

  count () {
    return Object.keys(this.errors).length
  }

  clear (field) {
    if (field) {
      return delete this.errors[field]
    }

    this.errors = {}
  }
}
