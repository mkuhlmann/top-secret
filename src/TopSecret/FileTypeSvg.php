<?php

namespace TopSecret;

use TopSecret\Model\Item;

class FileTypeSvg {
	private static $fileTypes = ["3g2","3ga","3gp","7z","aa","aac","ac","accdb","accdt","ace","adn","ai","aif","aifc","aiff","ait","amr","ani","apk","app","applescript","asax","asc","ascx","asf","ash","ashx","asm","asmx","asp","aspx","asx","au","aup","avi","axd","aze","bak","bash","bat","bin","blank","bmp","bowerrc","bpg","browser","bz2","bzempty","c","cab","cad","caf","cal","cd","cdda","cer","cfg","cfm","cfml","cgi","chm","class","cmd","code-workspace","codekit","coffee","coffeelintignore","com","compile","conf","config","cpp","cptx","cr2","crdownload","crt","crypt","cs","csh","cson","csproj","css","csv","cue","cur","dart","dat","data","db","dbf","deb","default","dgn","dist","diz","dll","dmg","dng","doc","docb","docm","docx","dot","dotm","dotx","download","dpj","ds_store","dsn","dtd","dwg","dxf","editorconfig","el","elf","eml","enc","eot","eps","epub","eslintignore","exe","f4v","fax","fb2","fla","flac","flv","fnt","folder","fon","gadget","gdp","gem","gif","gitattributes","gitignore","go","gpg","gpl","gradle","gz","h","handlebars","hbs","heic","hlp","hs","hsl","htm","html","ibooks","icns","ico","ics","idx","iff","ifo","image","img","iml","in","inc","indd","inf","info","ini","inv","iso","j2","jar","java","jpe","jpeg","jpg","js","json","jsp","jsx","key","kf8","kmk","ksh","kt","kts","kup","less","lex","licx","lisp","lit","lnk","lock","log","lua","m","m2v","m3u","m3u8","m4","m4a","m4r","m4v","map","master","mc","md","mdb","mdf","me","mi","mid","midi","mk","mkv","mm","mng","mo","mobi","mod","mov","mp2","mp3","mp4","mpa","mpd","mpe","mpeg","mpg","mpga","mpp","mpt","msg","msi","msu","nef","nes","nfo","nix","npmignore","ocx","odb","ods","odt","ogg","ogv","ost","otf","ott","ova","ovf","p12","p7b","pages","part","pcd","pdb","pdf","pem","pfx","pgp","ph","phar","php","pid","pkg","pl","plist","pm","png","po","pom","pot","potx","pps","ppsx","ppt","pptm","pptx","prop","ps","ps1","psd","psp","pst","pub","py","pyc","qt","ra","ram","rar","raw","rb","rdf","rdl","reg","resx","retry","rm","rom","rpm","rpt","rsa","rss","rst","rtf","ru","rub","sass","scss","sdf","sed","sh","sit","sitemap","skin","sldm","sldx","sln","sol","sphinx","sql","sqlite","step","stl","svg","swd","swf","swift","swp","sys","tar","tax","tcsh","tex","tfignore","tga","tgz","tif","tiff","tmp","tmx","torrent","tpl","ts","tsv","ttf","twig","txt","udf","vb","vbproj","vbs","vcd","vcf","vcs","vdi","vdx","vmdk","vob","vox","vscodeignore","vsd","vss","vst","vsx","vtx","war","wav","wbk","webinfo","webm","webp","wma","wmf","wmv","woff","woff2","wps","wsf","xaml","xcf","xfl","xlm","xls","xlsm","xlsx","xlt","xltm","xltx","xml","xpi","xps","xrb","xsd","xsl","xspf","xz","yaml","yml","z","zip","zsh"];

	public static function get($item, $bg = '#333') {
		$ext = strtolower($item->extension);

		if(in_array($ext, self::$fileTypes)) {
			return file_get_contents(app()->path('vendor/dmhendricks/file-icon-vectors/dist/icons/vivid/' . $ext . '.svg'));
		}

		switch($item->type) {
			default:
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
