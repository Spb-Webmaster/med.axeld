export function scrf() {
    /** получаем csrf **/
    return  document.querySelector('meta[name="csrf-token"]').content;
    /** получаем csrf **/
}
