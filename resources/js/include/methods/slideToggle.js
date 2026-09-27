import {slideDown} from './slideDown'
import {slideUp} from './slideUp'
export function slideToggle(target, duration = 500) {

        if (window.getComputedStyle(target).display === 'none') {
            return slideDown(target, duration);
        } else {
            return slideUp(target, duration);
        }

}
