export const DateUtils = {
  formatDate: function (value) {
    value = value.date
    if (value) {
      const date = new Date(value)
      const day = date.getDate()
      const monthIndex = date.getMonth()
      const year = date.getFullYear()
      return year + '-' + ('0' + (monthIndex + 1)).slice(-2) + '-' + ('0' + day).slice(-2)
    }
  }
}
