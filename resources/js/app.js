import '../../public/assets/css/vendor/dropzone.min.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import '../../public/assets/css/vendor/bootstrap.min.css'
import '../../public/assets/css/vendor/bootstrap.rtl.only.min.css'
import 'video.js/dist/video-js.css'

import { defaultColor, themeSelectedColorStorageKey } from './constants/config'

var color = defaultColor

if (localStorage.getItem(themeSelectedColorStorageKey)) {
  color = localStorage.getItem(themeSelectedColorStorageKey);
}

let render = () => {
  import('../../public/assets/css/sass/themes/piaf.' + color + '.scss').then(() =>
    require('./main')
  );
};

render();