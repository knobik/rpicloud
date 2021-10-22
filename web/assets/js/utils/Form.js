import ErrorBag from './ErrorBag'
import Api from './Api'

export default class {
  originalData

  constructor (data) {
    this.originalData = data

    for (const field in this.originalData) {
      this[field] = this.originalData[field]
    }

    this.errors = new ErrorBag()

    this.reset()
  }

  reset () {
    for (const field in this.originalData) {
      this[field] = this.originalData[field]
    }
  }

  fill (data) {
    for (const field in this.originalData) {
      this[field] = data[field]
    }
  }

  upload (field, $event) {
    this[field] = $event.target.files
  }

  data () {
    const data = new FormData()
    for (const field in this.originalData) {
      // if we have a filelist, then we add all the files to the form and continue
      if (this[field] instanceof FileList) {
        for (let i = 0; i < this[field].length; i++) {
          data.append(field, this[field][i])
        }

        continue
      }

      data.append(field, this[field])
    }

    return data
  }

  delete (url) {
    return this.submit('delete', url)
  }

  patch (url) {
    return this.submit('patch', url)
  }

  put (url) {
    return this.submit('put', url)
  }

  post (url) {
    return this.submit('post', url)
  }

  submit (requestType, url) {
    return new Promise((resolve, reject) => {
      Api[requestType.toLowerCase()](url, this.data(), {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
        .then((response) => {
          this.onSuccess(response)
          resolve(response)
        })
        .catch((error) => {
          this.onFailure(error.response)
          reject(error.response)
        })
    })
  }

  onSuccess (data) {
    this.reset()
    this.errors.clear()
  }

  onFailure (data) {
    console.log(data.data.data)
    this.errors.record(data.data.data)
  }
}
