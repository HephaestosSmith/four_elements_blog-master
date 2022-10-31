import { createStore } from 'vuex'
import axios from 'axios'
import Cookies from 'vue-cookie'

import Prism from "prismjs";
import 'prismjs/components/prism-bash'
import 'prismjs/components/prism-javascript'
import 'prismjs/components/prism-json'
import 'prismjs/components/prism-liquid'
import 'prismjs/components/prism-markdown'
import 'prismjs/components/prism-markup-templating'
import 'prismjs/components/prism-php'
//編輯器樣式
import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor'
import '@ckeditor/ckeditor5-build-classic/build/translations/zh'

import heading from '@ckeditor/ckeditor5-heading/src/heading'
import listStyle from '@ckeditor/ckeditor5-list/src/listproperties'
import font from '@ckeditor/ckeditor5-font/src/font'
import alignment from '@ckeditor/ckeditor5-alignment/src/alignment'
import blockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote'
import codeBlock from '@ckeditor/ckeditor5-code-block/src/codeblock'
import linkPlugin from '@ckeditor/ckeditor5-link/src/link'
import horizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline'
import mediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed'

// basic-styles
import bold from '@ckeditor/ckeditor5-basic-styles/src/bold'
import italic from '@ckeditor/ckeditor5-basic-styles/src/italic'
import strikethrough from '@ckeditor/ckeditor5-basic-styles/src/strikethrough'
import underline from '@ckeditor/ckeditor5-basic-styles/src/underline'
import subscript from '@ckeditor/ckeditor5-basic-styles/src/subscript'
import superscript from '@ckeditor/ckeditor5-basic-styles/src/superscript'
import code from '@ckeditor/ckeditor5-basic-styles/src/code'

// Table
import table from '@ckeditor/ckeditor5-table/src/table'
import tableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar'

// indent
import indent from '@ckeditor/ckeditor5-indent/src/indent'
import indentBlock from '@ckeditor/ckeditor5-indent/src/indentblock'

// 人名tag功能
import mention from '@ckeditor/ckeditor5-mention/src/mention'

//
import Highlight from '@ckeditor/ckeditor5-highlight/src/highlight';
import Base64UploadAdapter from '@ckeditor/ckeditor5-upload/src/adapters/base64uploadadapter';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize';
import LinkImage from '@ckeditor/ckeditor5-link/src/linkimage';
import ImageInsert from '@ckeditor/ckeditor5-image/src/imageinsert';
import SimpleUploadAdapter from '@ckeditor/ckeditor5-upload/src/adapters/simpleuploadadapter';
import 'prismjs/components/prism-scss'
import "prismjs/themes/prism-tomorrow.css"; // you can change


axios.defaults.withCredentials = true;
axios.defaults.baseURL='/api'
//app.provide('axios', axios);
//app.provide('phpurl', function(name) {    
//  return name = '/controllers/' + name + '.php';
//})

export default createStore({
  state: {
    axios:axios,
    phpPath:'/controllers/',
    phpType:'.php',
    Cookies:Cookies,
    logined:false,
    list:[],
    KEYWORD:'',
    noDataFlag:false,
    SEARCHTYPE:'default',
    Createflag:false,
    modalloadflag:true,
    homeloadflag:true,
    MAINCATEGORYS:[],
    SUBCATEGORYS:[],
    CKEditor:ClassicEditor,
    editorConfig: {
      simpleUpload: {
            // The URL that the images are uploaded to.
            uploadUrl: '../api/controllers/upload.php',

            // Enable the XMLHttpRequest.withCredentials property.
            withCredentials: false,

            // Headers sent along with the XMLHttpRequest to the upload server.
            headers: {
                'X-CSRF-TOKEN': 'CSRF-Token',
                Authorization: 'Bearer <JSON Web Token>'
            }
        },
      image: {
        toolbar: [ 
          'imageStyle:inline',
          'imageStyle:block',
          'imageStyle:side','toggleImageCaption', 'imageTextAlternative' ]
      },
      plugins: [
        heading,
        bold,
        italic,
        strikethrough,
        underline,
        subscript,
        superscript,
        code,
        listStyle,
        font,
        alignment,
        blockQuote,
        codeBlock,
        table,
        tableToolbar,
        linkPlugin,
        indent,
        indentBlock,
        horizontalLine,
        mediaEmbed,
        mention,
        Highlight,
        Base64UploadAdapter,
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageResize,
        LinkImage,
        ImageInsert,
        SimpleUploadAdapter
      ],
      toolbar: {
        items: [
          'heading', // 標題、次標題、次次標題
          '|',
          // 粗體、斜體
          'bold',
          'italic',
          '|',
          // 刪除線、下底線、上標、下標
          'strikethrough',
          'underline',
          'subscript',
          'superscript',
          '|',
          // 編號排版、符號排版種
          'bulletedList',
          'numberedList',
          '|',
          //文字顏色、文字底色、文字字形、文字大小、文字對齊
          'fontColor',
          'fontBackgroundColor',
          'fontfamily',
          'fontsize',
          'alignment',
          '|',
          'blockQuote', // 引用
          'code', // 代碼工具欄
          'codeBlock', // 程式碼區塊
          'insertTable', // 表格(合併、分隔、表格底色、刪除欄/列、插入欄列)
          'horizontalLine', // 水平線
          'link', // 超連結
          '|',
          // 縮排
          'outdent',
          'indent',
          '|',
          'imageinsert',
          'mediaEmbed', // 插入媒體
          'highlight',
          '|',
          'imageStyle:inline',
          'imageStyle:block',
          'imageStyle:side',
          '|',
          'toggleImageCaption',
          'imageTextAlternative',
          '|',
          'linkImage'
        ]
      },
      mention: {
        feeds: [
          {
            marker: '@',
            feed: [
              '@Anila',
              '@Amy',
              '@Andrea',
              '@Angela',
              '@Ann',
              '@Anna',
            ],
            minimumCharacters: 1
          }
        ]
      },
      
    mediaEmbed: {
      previewsInData: true
  }
  }
  },
  getters: {
    phpurl: (state) => (name) => {
      return state.phpPath + name + state.phpType
    }
  },
  mutations: {
    ModalViewColse(){  //原按鈕事件程式碼 用來關閉modal用 輸入ESC鍵
      var el = document.getElementById("ModalView"),
          evtType = 'keydown',
          keyCode = 27,
          doc = el.ownerDocument,
          win = doc.defaultView || doc.parentWindow,
          evtObj;
      if(doc.createEvent){
          if(win.KeyEvent) {
              evtObj = doc.createEvent('KeyEvents');
              evtObj.initKeyEvent( evtType, true, true, win, false, false, false, false, keyCode, 0 );
          }
          else {
              evtObj = doc.createEvent('UIEvents');
              Object.defineProperty(evtObj, 'keyCode', {
          get : function() { return this.keyCodeVal; }
      });     
      Object.defineProperty(evtObj, 'which', {
          get : function() { return this.keyCodeVal; }
      });
      evtObj.initUIEvent( evtType, true, true, win, 1 );
      evtObj.keyCodeVal = keyCode;
      if (evtObj.keyCode !== keyCode) {
          console.log("keyCode " + evtObj.keyCode + " 和 (" + evtObj.which + ") 不匹配");
      }
          }
          el.dispatchEvent(evtObj);
      } 
      else if(doc.createEventObject){
          evtObj = doc.createEventObject();
          evtObj.keyCode = keyCode;
          el.fireEvent('on' + evtType, evtObj);
      }
   },
   PrismView(){
    Prism.highlightAll(); 
   }
  },
  actions: {
  },
  modules: {
  }
})
