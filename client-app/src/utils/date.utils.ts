import { date } from 'quasar'

export default class DateUtils {
  static Q_DATE_FORMAT: 'YYYY/MM/DD'

  static dateToQDate (dateParam: Date | string): string {
    return date.formatDate(dateParam, this.Q_DATE_FORMAT)
  }

  static qDateToDate (qDate: string): Date {
    return date.extractDate(qDate, this.Q_DATE_FORMAT)
  }

  static dateToHeure (dateParam: Date | string): string {
    return date.formatDate(dateParam, 'HH:mm')
  }

  static dateToJourComplet (dateParam: Date | string): string {
    return date.formatDate(dateParam, 'dddd DD MMMM')
  }
}
