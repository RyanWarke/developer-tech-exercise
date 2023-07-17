import pluralize from 'pluralize';

export default function (value, number, showCount = true) {
  const formattedNumber = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return showCount ? `${formattedNumber} ${pluralize(value, number)}` : pluralize(value, number);
}