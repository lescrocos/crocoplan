import { date } from 'quasar'

export namespace dateUtils {
  export const Q_DATE_FORMAT = 'YYYY/MM/DD'

  export function dateToQDate(dateParam: Date): string {
    return date.formatDate(dateParam, Q_DATE_FORMAT)
  }

  export function qDateToDate(qDate: string): Date {
    return date.extractDate(qDate, Q_DATE_FORMAT)
  }

  export function dateToHeure(dateParam: Date): string {
    return date.formatDate(dateParam, 'HH:mm')
  }

  export function dateToJourComplet(dateParam: Date): string {
    return date.formatDate(dateParam, 'dddd DD MMMM')
  }

}
