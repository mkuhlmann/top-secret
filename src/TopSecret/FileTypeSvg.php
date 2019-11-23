<?php

namespace TopSecret;

class FileTypeSvg {

    public static function get($filetype, $bg = '#333') {
        switch($filetype) {
            case 'binary':
                return '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><path fill="' . $bg .'" d="M105.714 431.333V80.667h225.428l75.143 75.142v275.524H105.714zm275.524-250.476l-75.143-75.143H130.762v300.571h250.476V180.857zM230.952 256h-75.143V155.81h75.143V256zm-25.048-75.143h-25.047v50.095h25.047v-50.095zm0 175.333h25.048v25.048h-75.143V356.19h25.048v-50.095h-25.048v-25.048h50.095v75.143zm100.19-125.238h25.048V256H256v-25.048h25.047v-50.095H256v-25.048h50.095v75.143zm25.048 150.286H256v-100.19h75.142v100.19zm-25.047-75.143h-25.048v50.095h25.048v-50.095z" fill-rule="nonzero"/></svg>';
                break;
            case 'text':
                return '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><path fill="' . $bg .'" d="M218.392 205.857l-62.678 62.679 62.678 62.678 25.072-25.071-37.607-37.607 37.607-37.607-25.072-25.072zm50.143 25.072l37.607 37.607-37.607 37.607 25.072 25.071 62.678-62.678-62.678-62.679-25.072 25.072zM331.214 80.5H105.57v351h300.857V155.714L331.214 80.5zm50.143 325.929H130.642V105.57h175.5l75.215 75.215v225.643z" fill-rule="nonzero"/></svg>';
                break;
            case 'url':
                return '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><path fill="' . $bg .'" d="M356 356H156V156.745l50-.745v-50H106v300h300V281h-50v75zM256 106l50 50-75 75 50 50 75-75 50 50V106H256z" fill-rule="nonzero"/></svg>';
                break;
        }
    }
}