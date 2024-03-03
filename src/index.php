<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Eval</title>
    <link rel="icon" type="image/x-icon" href="img/elephant.png">
	<link rel="stylesheet" href="js/lib/jquery/jquery_ui_1.13.2.css">
	<script src="js/lib/jquery/jquery_3.6.0.js"></script>
	<script src="js/lib/jquery/jquery_ui_1.13.2.js"></script>
	
	<link rel="stylesheet" href="js/lib/jst/overlay_scrollbar.css">
	<link rel="stylesheet" href="js/lib/jst/jst_min_4.0.0.css">
	<script src="js/lib/jst/jst_4.0.0.js"></script>
	<script src="js/lib/jst/DivResizer.js"></script>
	<script src="js/lib/jst/Theme.js"></script>
	<script src="js/lib/jst/JQConnect.js"></script>
	
	<link rel="stylesheet" href="js/lib/bootstrap/bootstrap_min_5.3.0.css">
	<script src="js/lib/bootstrap/bootstrap_min_5.3.0.js"></script>
	
	<link rel="stylesheet" href="js/lib/material/material.css">
	
	<link rel="stylesheet" href="css/font-awesome.css">
	
	<script src="js/lib/tippy/popper_js_core_2.11.8.js"></script>
	<script src="js/lib/tippy/tippy_6.3.7.js"></script>
	
	<link href="js/lib/ace/ace_1.32.6.css" rel="stylesheet">
    <link rel="stylesheet" href="css/ace_print_margin_to_bracket_border.css">
	<script src="js/lib/ace/ace_min_1.32.6.js"></script>
	<script src="js/lib/ace/ace_ext_language_tools_1.32.6.js"></script>
	<script src="js/lib/ace/ace_ext_prompt_min_1.32.6.js"></script>
	<script src="js/lib/ace/ace_mode_php_1.32.6.js"></script>
	<script src="js/lib/ace/ace_ext_settings_menu_min_1.32.6.js"></script>
	<script src="js/lib/ace/ace_ext_search_box_min_1.32.6.js"></script>
	<script src="js/lib/ace/ace_snippets_php_1.32.6.js"></script>
	
	<script src="js/lib/vue_3.3.4.js"></script>

    <script src="js/Eval.js"></script>

    <script src="js/lib/colorbox/jquery.colorbox.js"></script>
	
	<style>
        @font-face {
            font-family: 'jetbrains_mono';
            src: url(font/jmono.woff2) format('woff2');
            font-style: normal;
            font-weight: normal;
        }

        :root {
            --body-bg: #F5F7FA;

            --header-bg: #191A1E;
            --header-color: rgb(221, 222, 226);
            --header-border: #191A1E;
            
            --sec-header-border: #6E7075;

            --file-sec-bg: #e3e4e5;
            --file-sec-color: black;

            --no-file-view-tip-bg: lightgray;
            
            --bar-bg-color: #F7F8FA;
            
            --output-benchmark-color: #E73408;
            --output-div-bg: white;
        }

        [data-bs-theme="dark"] {
            --body-bg: #0F111A;

            --header-bg: #2B2D30;
            --header-color: #AFB1B3;
            --header-border: #1E1F22;
            
            --sec-header-border: #43454A;

            --file-sec-bg: #1E1F22;
            --file-sec-color: lightgray;
            
            --no-file-view-tip-bg: #151618;

            --bar-bg-color: #2B2D30;
            
            --output-benchmark-color: #FC803A;
            --output-div-bg: #1E1F22;
        }

        body {
            margin: 0;
        }

        .header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 8px 12px;
            background-color: var(--header-bg);
            color: var(--header-color);
            height: 56px;
            z-index: 2;
        }

        #header-msg {
            padding: 6px 12px;
            border-radius: 5px;
            font-size: .87em;
            margin-right: 16px;
        }

        .sec {
            display: flex;
            background: #2B2D30;
            box-sizing: border-box;
        }

        .sec-header {
            padding: 12px;
        }

        .sec-body {
            flex-grow: 1;
            box-sizing: border-box;
        }

        #app {
            position: fixed;
            margin-top: 56px;
            width: 100% !important;
            height: 100% !important;
        }

        #file-sec {
            height: calc(100% - 56px);
            box-sizing: border-box;
            background-color: var(--file-sec-bg);
        }

        #code-sec {
            flex-grow: 1;
            box-sizing: border-box;
        }

        #result-sec {
            height: calc(100% - 56px);
            box-sizing: border-box;
            background-color: var(--file-sec-bg);
        }

        #editor {
            flex-grow: 1;
            width: 100%;
            height: 95%;
            display: none;
        }

        .ace_editor, #output-content {
            font-family: 'jetbrains_mono', monospace !important;
        }
        
        #output-content {
            font-size: 12px;
        }
        
        .output-div {
            padding: 6px;
            transition: background-color 500ms;
        }
        
        .output-benchmark {
        
        }

        .output-script-terminated {
            color: var(--output-benchmark-color);
        }
        
        #execute-loader {
            margin-right: 8px;
            padding: 2px 8px;
            border-radius: .2rem;
            background: #5383F2;
        }
        
        #execute-loader img {
            margin-right: 4px;
            width: 20px;
        }

        #no-file-view-tip {
            background-color: var(--no-file-view-tip-bg);
            user-select: none;
        }

        #output-scrollable {
        
        }

        .file {
            margin-left: 12px;
            margin-right: 12px;
            margin-bottom: 4px;
            padding: 0 0 0 12px;
            transition: background 250ms;
            user-select: none;
            color: var(--file-sec-color);
        }

        .file h6 {
            margin: 0;
        }
        
        .file-active h6 {
            font-weight: bold;
        }
        
        .action i {
            font-size: 12px;
            padding: 8px;
            border-radius: 5px;
            color: lightgray;
            transition: background 250ms;
        }
        
        .action i:hover {
            height: 100%;
            background-color: #407cca;
        }

        .file .action {
            visibility: hidden;
        }

        .file:hover, .file-active {
            background-color: #2E436E;
            color: antiquewhite;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .file-active .action {
            visibility: visible;
        }
        
        .file-active .action i {
            color: antiquewhite !important;
        }

        .file:hover .action {
            visibility: visible;
        }

        .bar {
            min-width: 2px;
            max-width: 2px;
            height: 100%;
            background-color: var(--sec-header-border);
            cursor: e-resize;
            transition: background-color 150ms !important;
        }
        
        .bar-drag, .bar:hover {
            background-color: #0a53be;
        }

        #pac-man {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            background-color: #1E1F22;
            z-index: 99999;
        }

        #pac-man img {
            border-radius: 2%;
        }

        div.shortcut-row div {
            width: 33.33%;
        }

        .shortcut {
            font-weight: bold;
        }
        
        .btn-borderless {
            color: var(--header-color);
            background-color: transparent;
            border-width: 0;
        }

        .btn-borderless:hover {
            background-color: #2E436E;
        }
	</style>
</head>
<body id="eval" data-bs-theme="dark">

<div id="pac-man">
	<div class="jst-lay-center w-100 h-100 jst-lay-column">
		<img src="img/bd%20flag.webp" alt="Loading...">
		<h1 class="jst-m-0 jst-mt-8">Eval - Experiment PHP</h1>
	</div>
</div>

<div id="modal-settings" class="jst-modal">
	<div class="jst-lay-xs jst-lay-column jst-p-16">
		<div class="jst-lay-xs">
			<div class="jst-me-8 jst-lay-xs jst-lay-column flex-grow-1">
				<label for="st-font-size" class="form-label jst-txt-sm jst-m-0">Font size</label>
				<input id="st-font-size" type="number" step="1" min="12" max="22" class="form-control form-control-sm">
			</div>
			<div class="jst-me-8 jst-lay-xs jst-lay-column flex-grow-1">
				<label for="st-font-line-height" class="form-label jst-txt-sm jst-m-0">Line height</label>
				<input id="st-font-line-height" type="number" min="1" step="0.1" max="3" class="form-control form-control-sm">
			</div>
			<div class="jst-lay-xs jst-lay-column flex-grow-1">
				<label for="st-tab-size" class="form-label jst-txt-sm jst-m-0">Tab size</label>
				<input id="st-tab-size" type="number" step="1" min="0" max="10" class="form-control form-control-sm">
			</div>
		</div>
        <div class="jst-lay-yc jst-mt-16">
            <div class="jst-lay-xs jst-lay-column flex-grow-1">
                <label for="st-font-size" class="form-label jst-txt-sm jst-m-0">Tabs &amp; Line Break</label>

                <div class="form-check">
                    <label for="st-show-whitespace" class="form-check-label jst-txt-xs" style="user-select: none">
                        <input class="form-check-input" type="checkbox" id="st-show-whitespace">
                        Show whitespace glyphs
                    </label>
                </div>

            </div>

            <div class="jst-ms-8 jst-lay-xc jst-lay-column flex-grow-1">
                <label for="editor-theme" class="form-label jst-txt-sm jst-m-0">Editor theme</label>
                <select class="form-select form-select-sm jst-me-8" id="editor-theme"></select>
            </div>
        </div>
        
        <div class="jst-lay-yc jst-mt-16">
            <div class="jst-lay-xs jst-lay-column flex-grow-1">
                <label for="st-font-size" class="form-label jst-txt-sm jst-m-0">File deletion</label>

                <div class="form-check">
                    <label for="st-file-del-confirm" class="form-check-label jst-txt-xs" style="user-select: none">
                        <input class="form-check-input" type="checkbox" id="st-file-del-confirm">
                        Ask before deleting files
                    </label>
                </div>

            </div>
        </div>
		
	</div>
</div>

<div id="modal-shortcuts" class="jst-modal">
	<div class="jst-lay-xs jst-lay-column">
		<h6 class="jst-pb-8" style="border-bottom: 1px solid #2E436E">Global Shortcuts</h6>
		<div class="shortcut-row jst-lay-xsb-yc">
			<div>
				<span class="shortcut jst-txt-sm">Alt + 1</span>
				<p class="jst-mt-0 jst-txt-xs">Toggle File Section</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + `</span>
				<p class="jst-mt-0 jst-txt-xs">Switch light/dark theme</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Esc</span>
				<p class="jst-mt-0 jst-txt-xs">Focus/blur the editor</p>
			</div>
		</div>
        <div class="shortcut-row jst-lay-xsb-yc">
            <div>
                <span class="shortcut jst-txt-sm">Ctrl + Alt + S</span>
                <p class="jst-mt-0 jst-txt-xs">Show settings window</p>
            </div>
            <div>
                <span class="shortcut jst-txt-sm">Alt + Insert</span>
                <p class="jst-mt-0 jst-txt-xs">New PHP script</p>
            </div>
            <div>
                <span class="shortcut jst-txt-sm">F5</span>
                <p class="jst-mt-0 jst-txt-xs">Reload files</p>
            </div>
        </div>
        <div class="shortcut-row jst-lay-xsb-yc">
            <div>
                <span class="shortcut jst-txt-sm">Ctrl + Enter</span>
                <p class="jst-mt-0 jst-txt-xs">Execute the current file</p>
            </div>
            <div>
                <span class="shortcut jst-txt-sm">Alt + F6</span>
                <p class="jst-mt-0 jst-txt-xs">Rename current file</p>
            </div>
            <div>
                <span class="shortcut jst-txt-sm">Alt + Shift + Delete</span>
                <p class="jst-mt-0 jst-txt-xs">Clear output</p>
            </div>
        </div>
        
		<h6 class="jst-mt-16 jst-pb-8" style="border-bottom: 1px inset #2E436E">Editor Shortcuts</h6>
		<div class="shortcut-row jst-lay-xs-yc">
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + P</span>
				<p class="jst-mt-0 jst-txt-xs">Jump to matching</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + Shift + M</span>
				<p class="jst-mt-0 jst-txt-xs">Expand to matching</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + Shift + L</span>
				<p class="jst-mt-0 jst-txt-xs">Expand to line</p>
			</div>
		</div>
		<div class="shortcut-row jst-lay-xsb-yc">
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + G</span>
				<p class="jst-mt-0 jst-txt-xs">Go to line</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + D</span>
				<p class="jst-mt-0 jst-txt-xs">Delete line</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + Shift + D</span>
				<p class="jst-mt-0 jst-txt-xs">Duplicate selection</p>
			</div>
		</div>
		<div class="shortcut-row jst-lay-xsb-yc">
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + U</span>
				<p class="jst-mt-0 jst-txt-xs">Change to upper case</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl-Shift-U</span>
				<p class="jst-mt-0 jst-txt-xs">Change to lower case</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + Shift + D</span>
				<p class="jst-mt-0 jst-txt-xs">Duplicate selection</p>
			</div>
		</div>
		<div class="shortcut-row jst-lay-xsb-yc">
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + +</span>
				<p class="jst-mt-0 jst-txt-xs">Increase font size</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + -</span>
				<p class="jst-mt-0 jst-txt-xs">Decrease font size</p>
			</div>
			<div>
				<span class="shortcut jst-txt-sm">Ctrl + 0</span>
				<p class="jst-mt-0 jst-txt-xs">Reset font size</p>
			</div>
		</div>
	</div>
</div>

<div id="modal_rename" class="jst-modal">
    <label for="filename-rename" class="w-100">
        Change file name:
        <input id="filename-rename" type="text" class="form-control form-control-sm">
    </label>
</div>

<div class="header jst-lay-xsb-yc">
	<div class="jst-lay-xs-yc">
<!--		<img src="img/elephant.png" alt="Gear" style="width: 38px; user-select: none;">-->
		<h4 class="jst-m-0">Eval - Experiment PHP8</h4>
	</div>
	<div class="jst-lay-xs-yc">
		<span id="msg"></span>
		<button data-tippy-content="Files [Alt+1]" onclick="toggleFileSec()" class="btn-borderless jst-me-4 jst-btn jst-btn-black"><i id="i-file" class="fas fa-folder"></i></button>
		<button data-tippy-content="Hati Doc [Alt+`]" onclick="toggleHatiDoc()" class="btn-borderless jst-me-4 jst-btn jst-btn-black"><i class="fas fa-elephant"></i></button>
		<button data-tippy-content="Theme [Ctrl+`]" onclick="Theme.toggle()" class="btn-borderless jst-me-4 jst-btn-black"><i id="i-theme"></i></button>
		<button data-tippy-content="Shortcuts" onclick="toggleShortcut()" class="btn-borderless jst-me-4 jst-btn-black"><i class="fas fa-keyboard"></i></button>
		<button data-tippy-content="Settings [Ctrl+Alt+S]" onclick="showSettings()" class="btn-borderless jst-btn jst-btn-black"><i class="fas fa-wrench"></i></button>
	</div>
</div>

<div id="app" class="jst-lay-xs">
	<div id="file-sec" class="sec jst-lay-column">
		<div class="sec-header">
			<div class="jst-lay-xsb-yc w-100">
                <input v-model="searchTerm" id="search-term" type="text" placeholder="Search files" class="form-control form-control-sm jst-me-8">
				<button @click="listFiles()" data-tippy-content="Reload files [F5]" class="jst-btn jst-btn-sm jst-btn-dark jst-me-8"><i class="fas fa-sync-alt"></i></button>
				<button @click="createFile()" data-tippy-content="New script [Alt + Insert]" class="jst-btn jst-btn-sm jst-btn-dark"><i class="fas fa-plus"></i></button>
			</div>
		</div>
		
		<div id="file-scrollable" class="sec-body">
			<div @click="loadFile(file)" v-for="file in cmpFiles" :key="file" :class="{'file-active': file === filename}" class="jst-lay-xs-yc file">
                <h6 class="jst-txt-sm flex-grow-1">{{file}}</h6>
				<div class="action jst-lay-yc">
					<i @click.stop="actionRename(file, 'rename')" class="fas fa-pen"></i>
					<i @click.stop="actionDelete(file)" class="fas fa-trash"></i>
				</div>
			</div>
            
            <div id="no-file-list" class="jst-txt-sm jst-lay-center h-100">
                No scripts
            </div>
		</div>
	</div>
	
    <div id="bar1" class="bar"></div>
    
	<div id="code-sec" class="sec">
		<div id="editor" style="height: calc(100% - 56px)"></div>
        <div class="jst-lay-center w-100" id="no-file-view-tip">
            <img style="width: 100px;" src="img/elephant.png" alt="No file">
            <div class="jst-ms-16">
                <h5 class="jst-mb-16">Welcome to Eval !</h5>
                <h6 class="jst-txt-sm jst-mb-8">Click <i class="fas fa-plus"></i> icon to create files [<b>Alt + Insert</b>]</h6>
                <h6 class="jst-txt-sm jst-mb-8">Hit <b>Ctrl + Enter</b> to execute code</h6>
                <h6 class="jst-txt-sm jst-mb-8">Customize editors/themes in settings [<b>Ctrl + Alt + S</b>]</h6>
                <h6 class="jst-txt-sm jst-mb-8">Learn available <i class="fas fa-keyboard"></i> shortcuts to speed things up</h6>
            </div>
        </div>
	</div>
    
    <div id="bar2" class="bar"></div>
	
	<div id="result-sec" class="sec jst-lay-column">

        <div class="sec-header">
            <div class="jst-lay-xs-yst">
                <h5 class="jst-m-0 flex-grow-1">Output</h5>
                
                <div class="jst-lay-xe-yst flex-grow-1">
                    <div id="execute-loader" class="jst-txt-sm jst-lay-xs-yc jst-bg-green text-light">
                        <img src="icon/hour_glass.gif" alt="Executing...">
                        <span id="execute-countdown">00:00</span>
                    </div>
                    <button data-tippy-content="Run [Ctrl + Enter]" @click.stop="executeFile()" class="jst-btn jst-btn-sm jst-btn-green jst-me-8"><i class="fas fa-play"></i> Run</button>
                    <button data-tippy-content="Clear output [Alt + Shift + Del]" onclick="clearOutput()" class="jst-btn jst-btn-sm jst-btn-dark"><i class="fas fa-broom"></i> Clear</button>
                </div>
                
            </div>
        </div>
   
        <div id="output-scrollable" class="sec-body" style="white-space: pre-line;">
            <div id="output-content"></div>
        </div>
	</div>
</div>

<div class="jst-modal" id="hati-doc-modal">
    <iframe src="docs/index.html" style="width: 100%; height: 100%"></iframe>
</div>

<script>
	const countdownTime = 30
    
    const debug = true
	
	const iconDone 		= `<i class="fas fa-check-circle"></i>`
	const iconWarning	= `<i class="fas fa-exclamation-triangle"></i>`
	const iconError		= `<i class="fas fa-times"></i>`
	const iconInfo		= `<i class="fas fa-info-circle"></i>`
 
	/*
	 * Vue App
	 * */
    const vue = {
		data() {
			return {
				executing: false,
				
				searchTerm: null,

				shortcuts: {
					
					hatiDoc: (e) => {
						// Alt + `
						let key = e.altKey && e.which === 192
                        if (!key) return false
                        
                        e.preventDefault()
                        toggleHatiDoc()
                        return true
                    },
                    
                    saveFile: async (e) => {
						// Ctrl + S
						let key = e.ctrlKey && e.which === 83
						if (!key) return false

						e.preventDefault()

						if (!this.filename) {
							setMsg('No file changed. Nothing needs saving', 0)
							return true
						}

						let saved = await this.saveChangeAsync()

						let obj = {}
						obj.msg = saved ? `${this.filename} saved` : `Failed saving ${this.filename}`
						obj.status = saved ? 1 : -1
						setMsg(obj.msg, obj.status)

						return true
					},
     
					fontResize: (e) => {
					 
						let keyCode = e.keyCode
						
						// Ctrl + +
					    let ctrlPlus  = e.ctrlKey && (keyCode === 61 || keyCode === 107)
                        if (ctrlPlus) {
							e.preventDefault()
							increaseFontSize()
							return true
                        }
                        
						// Ctrl + -
					    let ctrlMinus = e.ctrlKey && (keyCode === 109 || keyCode === 173)
                        if (ctrlMinus) {
							e.preventDefault()
							decreaseFontSize()
							return true
                        }
						
						// Ctrl + 0
					    let ctrlZero  = e.ctrlKey && (keyCode === 48 || keyCode === 96)
                        if (ctrlZero) {
							e.preventDefault()
                            setFontSize(defFontSize)
							return true
                        }
                        
						return false
                    },
                    
					createFile: (e) => {
						// Alt + Insert
						let key = e.altKey && e.which === 45
						if (!key) return false

						e.preventDefault()
						this.createFile()
						return true
					},
     
					clearOutput: (e) => {
						// Alt + Shift + Delete
						let key = e.altKey && e.shiftKey && e.which === 46
						if (!key) return false

						e.preventDefault()
						clearOutput()
						return true
					},
                    
					deleteFile: (e) => {
						// Alt + Delete
						let key = e.altKey && e.which === 46
						if (!key) return false

						e.preventDefault()
						this.actionDelete(this.filename)
						return true
					},

					renameFile: (e) => {
						// Alt + F6
						let key = e.altKey && e.which === 117
						if (!key) return false

						e.preventDefault()

						if (!this.filename) {
							setMsg('No active file to rename', 0)
							return true
						}

						this.actionRename(this.filename)
						return true
					},

					execute: async (e) => {
						// Ctrl + Enter
						let key = e.ctrlKey && e.keyCode === 13
						if (!key) return false

						e.preventDefault()
						await this.executeFile()
						return true
					},

					settings: (e) => {
						// Ctrl + Alt + S
						if (e.key !== 'ś') return false

						e.preventDefault()
						if (OverlayManager.isShowing('modal-settings')) return true

						showSettings()
						return true
					},

					files: (e) => {
						// Alt + 1
						let key = e.altKey && e.keyCode === 49
						if (!key) return false

						e.preventDefault()
                        toggleFileSec()
						return true
					},

					theme: (e) => {
						// Ctrl + `
						let key = e.ctrlKey && e.keyCode === 192
						if (!key) return false

						e.preventDefault()
						Theme.toggle()
						return true
					},

					editorFocus: (e) => {
						// Esc
						if (e.keyCode !== 27) return false

						e.preventDefault()

                        let active = editorDom.css('display') !== 'none'
							
						if (editor.isFocused()) {
							editor.blur()
                            if (active) setMsg(`Editor lost focus 👀⏳🐘`, 2)
						} else {
						    editor.focus()
                            if (active) setMsg(`Let's experiment 🧪🧫🔬`, 0)
                        }
                        

						return true
					},
					
					reloadFiles: (e) => {
						let key = e.key === 'F5'
						if (!key) return false

						e.preventDefault()
						this.listFiles()
						return true
					},
				},

				files: [],

				// Holds the name of the current file in use
				filename: '',

				// Holds the filename which to be renamed
				filenameRename: '',
			}
		},

		computed: {
			cmpFiles () {
				if (this.searchTerm === null) return this.files
				let keyword = this.searchTerm.toLowerCase().trim();
				return this.files.filter(item => item.toLowerCase().includes(keyword));
			}
		},

		watch: {
			filename () {
				this.toggleFileSecHint(this.filename === null)
			}
		},

		methods: {
			
			async executeFile () {
				if (!this.filename) {
					setMsg('No file to run 😎', 0)
					this.executing = false
                    return
                }
				
				if (this.executing) {
					setMsg('Still executing...⏳😒', 2)
					return
				}

				this.executing = true
				setMsg('Executing... ⏳', 0, .5)
				showTimer()

				let saved = await this.saveChangeAsync()
				if (!saved) {
					// TODO - change with Dialog
					alert(`Could not save ${this.filename} to execute`)

					stopTimer()

					this.executing = false
					return
				}

				let promise = JQConnect()
					.timeout(120 * 60 * 1000) // Never timing out!
					.to('https://localhost/eval/api/eval/script/execute')
					.queryParam('file', this.filename)

				await promise.getAsync()

				if (promise.isErr()) {
					alert('Failed to execute the script!')

					stopTimer()

					this.executing = false
					return
				}

				let output = promise.response().output
				outputContentDom.append(`<div class="output-div">${output}</div>`)

				outputScroller.scroll({y: "100%"}, 250)

				stopTimer()

				this.executing = false
            },

			async listFiles () {
				// TODO - fix this DOM issue!
				// setMsg('Reloading file lists...', 0, .5)

				await this.saveChangeAsync()

				dLog('Fetching files...')
				let promise = await Eval.list()

				if (promise.isErr()) {
					dLog('Failed to fetch file lists')
					setMsg('Failed to fetch file lists', -1)
					return
				}

				dLog('File lists fetched')
				this.files = promise.response().files

				/*
				 * Load the last opened file
				 * */
				let filename = Biscuit.getStr('filename', null)

				/*
				 * Toggle the file section hint accordingly
				 * */
				let emptyList = this.files.length === 0
				if (!emptyList && filename === null) {
					editorDom.hide()
					$('#no-file-view-tip').show()
					$('#no-file-list').hide()
				}

				// No last opened file!
				if (filename === null) {
					dLog('No last opened file')
					return
				}

				/*
				 * Do we still have the last opened file?
				 * */
				let index = this.files.indexOf(filename)
				if (index === -1) {
					dLog('last opened file already deleted')
					Biscuit.set('filename', null)
					return
				}

				await this.loadFileAsync(filename)
			},
			
			async createFile () {
				let saveChange = await this.saveChangeAsync()
                if (!saveChange) {
                    fileSavedErrDialog(this.filename)
                    return
                }
                
				dLog('Creating a file...')
                let promise = await Eval.create()
                
                let res = promise.response()
				setMsg(res.response.msg, res.response.status)
                
                if (promise.isErr()) {
					dLog('Failed to create file')
					return
				}

				let filename = res.filename
				this.files.push(filename)
                dLog(`${filename} created`)
    
				await this.loadFileAsync(filename)
                
                /*
                 * Scroll to the file we've just created now
                 * */
				fileScroller.scroll({y: "100%"}, 250)
			},
            
            async saveChangeAsync () {
                if (!this.filename) {
					dLog('No file to save, returning...')
					return true
				}
    
				dLog(`Saving ${this.filename}...`)
                let promise = await Eval.save(this.filename, editor.getValue())
                let ok = promise.isOk()
                
                if (ok) {
					dLog(`${this.filename} saved`)
                } else {
					dLog(`Failed to save ${this.filename}`)
                }
				
				return ok
            },
            
            async loadFileAsync (filename) {
				dLog(`Fetching ${filename}...`)
                let promise = await Eval.fetch(filename)
                
                if (promise.isErr()) {
					let msg = `Failed to fetch ${filename}`
					dLog(msg)
					setMsg(msg, -1)
					return
                }
				
				dLog(`${filename} fetched`)
				Biscuit.set('filename', filename)
                this.filename = filename
                
				let code = promise.response().data
                editor.setValue(code, -1)
                
				/*
				 * TODO - have the API return it!
                 * Restore last cursor position
                 * */
				// let cRow = Biscuit.getInt('cursor_row', 0)
				// let cCol = Biscuit.getInt('cursor_col', 0)
				// editor.moveCursorTo(cRow, cCol)
                
                editor.gotoLine(editor.session.getLength(), 4, true)

				jst.runLater(.5, () => editor.focus())
                dLog(`${filename} loaded in editor`)
            },

			async loadFile (filename) {
				// No loading for the same file already being edited >:(
				if (filename === this.filename) {
					dLog(`${filename} is already loaded, returning...`)
					return
				}
				
				// Save current file being edited!
				let saved = await this.saveChangeAsync()
				if (!saved) {
					fileSavedErrDialog(this.filename)
					return
				}

				await this.loadFileAsync(filename)
			},

			async renameFile (newFileName) {

				if (newFileName.length === 0) {
					Toast.info(`File name can't be empty`)
					return
				}

				if (!newFileName.endsWith('.php')) {
					newFileName += '.php'
				}

				dismissPopup('modal_rename')

				dLog(`Renaming ${this.filenameRename} to ${newFileName}...`)
				let promise = await Eval.rename(this.filenameRename, newFileName)

				let res = promise.response()
				let status = res.response.status

				setMsg(res.response.msg, status)

				if (status !== 1) {
					dLog(`Failed renaming from ${this.filenameRename} to ${newFileName}`)
					return
				}
				
				dLog(`Renamed from ${this.filenameRename} to ${newFileName}`)

				/*
				 * Update the file name at the right position in the file list array
				 * */
				let index = this.files.indexOf(this.filenameRename)
				if (index === -1) return

				this.files[index] = newFileName

				/*
				 * Refresh the editor with the renamed file!
				 * */
				if (this.filename === this.filenameRename) {
					await this.loadFileAsync(newFileName)
				}
			},
            
            async deleteAsync (filename) {
				let promise = await Eval.delete(filename)
                
                dismissPopup('del_confirm')
                
                if (promise.isErr()) {
					let msg = `Failed deleting ${filename}`
                    dLog(msg)
					setMsg(msg, -1)
					return
                }
				
				dLog(`${filename} deleted`)
				let res = promise.response().response
				setMsg(res.msg, res.status)

                /*
                 * Remove the file from the lists
                 * */
				let cPos = this.files.indexOf(filename)
				this.files.erase(filename)

                /*
                 * Did user just deleted the very last file?
                 * */
				let total = this.files.length
				if (total === 0) {
					dLog('Deleted the very last file')
					Biscuit.set('filename', null)
					this.filename = null
					return
				}
    
				/*
				 * If user deletes the active file loaded in the editor,
				 * then load the next available script!
				 * */
				if (this.filename === filename) {
					
					/*
					 * Get the next filename; if none then get the previous one
					 * */
					if (cPos === total) {
						cPos --;
					}

					let newFilename = this.files[cPos]
                    dLog(`${newFilename} is to load now`)

                    await this.loadFileAsync(newFilename)
				}
            
            },
            
            async actionDelete (filename) {
				if (this.files.length === 0) {
					let msg = 'Nothing to delete';
					dLog(msg)
					setMsg(msg, 2)
					return
				}

				let confirming = Biscuit.getBool('file_del_confirm', true)
				if (!confirming) {
					await this.deleteAsync(filename)
					return
				}

				let d = Dialog('del_confirm')
				d.title('Delete Confirmation')
				d.msg(`Are you sure you want to delete ${filename}?<br>This can't be undone!`)

				if (Theme.isDark()) d.dark()

				d.yes(() => this.deleteAsync(filename))
				d.no(() => d.dismiss())
				d.show()
            },
            
            actionRename (filename) {
				let m = Modal('modal_rename')
				m.title('Rename')

				if (Theme.isDark()) m.dark()

				this.filenameRename = filename

				fileRenameDom.val(filename)
				fileRenameDom.focus()

				m.show()

				fileRenameDom.select()
            },

			toggleFileSecHint (show) {
				if (show) {
					editorDom.hide()
					$('#no-file-view-tip').show()
					$('#no-file-list').show()
				} else {
					$('#no-file-view-tip').hide()
					$('#no-file-list').hide()
					editorDom.show()
				}
			},
   
		},

		created () {
			this.listFiles()
		},

		mounted () {
			/*
			 * Attach shortcuts
			 * */
			$(document).keydown(async (e) => {
				for (let f in this.shortcuts) {
					if (await this.shortcuts[f](e)) break
				}
			})

			/*
			 * Attach Enter key listener to file rename input
			 * */
			jst.runLater(1, () => {
				$(fileRenameDom).keydown((e) => {
					// Enter
					if (e.keyCode !== 13) return
					e.preventDefault()

					this.renameFile(fileRenameDom.val())
				})
			})

			/*
			 * Save current file before closing tab
			 */
			$(window).on('beforeunload', () => {
				jst.sleep(0.01) // COOL TRICK?? 👀
                if (editor.getValue().length === 0) return
				this.saveChangeAsync()
                log('Eval session saved')
			})
		}
	}
	
	const app = Vue.createApp(vue)
	app.mount('#eval')
    
    const editorDom = $('#editor')
	
	const defFontSize = 14
	const defMinFontSize = 12
	const defMaxFontSize = 22

	const defLineHeight = 1.5
	const defMinLineHeight = 1
	const defMaxLineHeight = 3

	const defTabSize = 4
	const defTabSizeMax = 10
	const defTabSizeMin = 0
 
	const themes = [
		"ambiance", "chaos", "chrome", "cloud9_day", "cloud9_night", "cloud9_night_low_color", "clouds", "clouds_midnight",
		"cobalt", "crimson_editor", "dawn", "dracula", "dreamweaver", "eclipse", "github", "gob", "gruvbox",
		"gruvbox_dark_hard", "gruvbox_light_hard", "idle_fingers", "iplastic", "katzenmilch", "kr_theme", "kuroir",
		"merbivore", "merbivore_soft", "mono_industrial", "monokai", "nord_dark", "one_dark", "pastel_on_dark",
		"solarized_dark", "solarized_light", "sqlserver", "terminal", "textmate", "tomorrow", "tomorrow_night",
		"tomorrow_night_blue", "tomorrow_night_bright", "tomorrow_night_eighties", "twilight", "vibrant_ink", "xcode"
	]

	const fontSizeDom = $('#st-font-size')
	const fontLineHeightDom = $('#st-font-line-height')
	const showWhitespaceDom = $('#st-show-whitespace')
	const tabSizeDom = $('#st-tab-size')
	const fileDelDom = $('#st-file-del-confirm')

	const fileSecDom    = $('#file-sec')
	const codeSecDom    = $('#code-sec')
	const resultSecDom  = $('#result-sec')

	const themeIconDom = $('#i-theme')
 
	const outputDom = $('#output-scrollable')
    const outputContentDom = $('#output-content')
    
    const executeInfo = $('#execute-loader')
    const countDown = $('#execute-countdown')

	const fileRenameDom = $('#filename-rename')

	const bar1 = $('#bar1')
	const bar2 = $('#bar2')
	
	const msgDom = $('#msg')

	/*
     * Init custom scrollbar for file & output section!
     * */
	const fileScroller = jst.overlayScrollbar($('#file-scrollable')[0])
	const outputScroller = jst.overlayScrollbar(outputDom[0])

	let editor;

	/*
	 * File window properties
	 * */
	let fwShown = true
	let fwAnimating = false

    /*
     * Script execution timer object
     * */
	let timer = null

    function initAce () {
        ace.require("ace/ext/language_tools")
		editor = ace.edit("editor");
		editor.getSession().setMode("ace/mode/php")
		editor.focus()
		editor.renderer.setPadding(0);

		editor.setOptions({
            printMargin: false,
			enableBasicAutocompletion: true,
			enableLiveAutocompletion: true,
			enableSnippets: true,
			cursorStyle: "smooth",
			enableAutoIndent: true,
			animatedScroll: true,
		})

        /*
         * Allow tabs everywhere even when users use arrow key to navigate cursors!
         * */
		editor.setBehavioursEnabled(true);
		editor.getSession().setUseSoftTabs(false);

		// Don't come in my way for shortcut Alt + Delete !
		editor.commands.removeCommand("removetolineend")
    }
    
	function initUITheme () {
		Theme.load()
		Theme.listenChange(() => $(themeIconDom).toggleClass('fad fa-moon fas fa-sun'))

		/*
		 * Set the theme button icon
		 * */
		let iconCls = Theme.isDark() ? 'fas fa-sun' : 'fad fa-moon'
		$(themeIconDom).addClass(iconCls)
	}

	function initWindowPref() {
		/*
		 * Restore last saved windows preference
		 * */
		let fw = Biscuit.getFloat('fw', 20)
		let cw = Biscuit.getFloat('cw', 60)
		let rw = Biscuit.getFloat('rw', 20)

		fileSecDom.width(`${fw}%`)
		codeSecDom.width(`${cw}%`)
		resultSecDom.width(`${rw}%`)

		if (Biscuit.getBool('file-sec-hidden', false)) {
			fwShown = false

			$(fileSecDom).css('width', '0')
			$(bar1).hide()
		}

		/*
		 * Add div drag to resize events to divs
		 * */
		DivResizer.hook({
            name: 'ab',
            bar: bar1,
            owner: codeSecDom,
            other: fileSecDom,
            ownerMinSize: 500,
            otherMinSize: 250,
            style: 'bar-drag'
		})
  
		DivResizer.hook({
            name: 'bc',
            bar: bar2,
            owner: resultSecDom,
            other: codeSecDom,
            ownerMinSize: 300,
            otherMinSize: 500,
            style: 'bar-drag'
        })

		/*
		 * Attach callback when user will have done dragging divs to save it
		 * */
		DivResizer.listenToDragDone(() => saveWinSizePref())
	}

	function initFont (editor) {
		/*
		 * Load last saved font size & line height
		 * */
		setFontSize(Biscuit.getInt('font_size', defFontSize))
		setLineHeight(Biscuit.getFloat('font_line_height', defLineHeight))
		setShowWhitespace(Biscuit.getBool('show_whitespace', false))
		setTabSize(Biscuit.getInt('tab_size', 4))

		editor.commands.addCommand({
			name: "increaseFontSize",
			bindKey: { win: "Ctrl-+", mac: "Command-+" },
			exec: () => increaseFontSize()
		})

		editor.commands.addCommand({
			name: "decreaseFontSize",
			bindKey: { win: "Ctrl--", mac: "Command--" },
			exec: () => decreaseFontSize()
		})

		editor.commands.addCommand({
			name: "resetFontSize",
			bindKey: { win: "Ctrl-numpad0", mac: "Command-0" },
			exec: () => setFontSize(defFontSize)
		})
	}

	function initTheme (editor) {
		/*
		 * Load editor theme
		 * */
		let currentTheme = Biscuit.getStr('editor_theme', 'tomorrow_night')
		editor.setTheme(`ace/theme/${currentTheme}`)
  
		let themeOptions = ''
		themes.forEach((i) => {
			let isCurrent = i === currentTheme ? 'selected' : ''
			themeOptions += `<option value="${i}" ${isCurrent}>${i}</option>`
		})

		let editorThemeSelect = $('#editor-theme')
		editorThemeSelect.html(themeOptions)

		$(editorThemeSelect).on('change', () => {
			currentTheme = $(editorThemeSelect).val()
			editor.setTheme(`ace/theme/${currentTheme}`)

			Biscuit.set('editor_theme', currentTheme)
		})
	}
	
	function initTippy () {
		// Init Tippy library
		tippy('[data-tippy-content]', { inertia: true });
    }
	
	function listenToAceEvents () {
		/*
		 * Save the current line number in a cookie called "cursorPosition"
		 * */
		editor.getSession().selection.on("changeSelection", function() {
			let position = editor.getCursorPosition()
			Biscuit.set('cursor_row', position.row)
			Biscuit.set('cursor_col', position.column)
		})

        /*
         * Allow Ctrl + G shortcut to jump to line number
         * */
		editor.commands.addCommand({
			name: 'Go to line',
			bindKey: {win: 'Ctrl-G',  mac: 'Command-G'},
			exec: (editor) => {
				let lineNumber = parseInt(prompt("Enter line number: "), 10);
				
				if (isNaN(lineNumber)) {
					setMsg('Invalid line number', 2)
					return
                }
				
				editor.gotoLine(lineNumber);
			},
			readOnly: true
		});
    }

	function listenToFontChanges () {
		fontSizeDom.keyup(() => setFontSize(fontSizeDom.val()))
		fontSizeDom.change(() => setFontSize(fontSizeDom.val()))

		fontLineHeightDom.keyup(() => setLineHeight(fontLineHeightDom.val()))
		fontLineHeightDom.change(() => setLineHeight(fontLineHeightDom.val()))
	}

	function listenToShowWhitespaceChange () {
		showWhitespaceDom.change(() => setShowWhitespace(showWhitespaceDom.is(':checked')))
	}

	function listenToFileDelConfirmChange () {
		fileDelDom.change(() => setFileDelConfirm(fileDelDom.is(':checked')))
	}

	function listenToTabSizeChange () {
		tabSizeDom.keyup(() => setTabSize(tabSizeDom.val()))
		tabSizeDom.change(() => setTabSize(tabSizeDom.val()))
	}

	function setFontSize (fontSize) {
		try {
			let size = parseInt(fontSize)

			if (size < defMinFontSize || size > defMaxFontSize) return

			editor.setFontSize(size)
			Biscuit.set('font_size', size)
		} catch (e) {
		}
	}

	function setLineHeight (lineHeight) {
		try {
			let size = parseFloat(lineHeight)

			if (size < defMinLineHeight || size > defMaxLineHeight) return

			$('.ace_editor').css('line-height', size)
			Biscuit.set('font_line_height', size)
		} catch (e) {
		}
	}

	function setTabSize (tabSize) {
		editor.getSession().setTabSize(tabSize)
		Biscuit.set('tab_size', tabSize)
	}

	function setShowWhitespace (val) {
		editor.setShowInvisibles(val);
		Biscuit.set('show_whitespace', val)
	}

	function setFileDelConfirm (val) {
        Biscuit.set('file_del_confirm', val)
	}

	function showSettings () {
		/*
		 * Load settings to the UI
		 * */
		fontSizeDom.val(Biscuit.getInt('font_size', defFontSize))
		fontLineHeightDom.val(Biscuit.getFloat('font_line_height', defLineHeight))
		$(tabSizeDom).val(Biscuit.getInt('tab_size', defTabSize))

		$(showWhitespaceDom).prop('checked', Biscuit.getBool('show_whitespace', false))
		$(fileDelDom).prop('checked', Biscuit.getBool('file_del_confirm', true))

		/*
		 * Show the settings modal
		 * */
		let modal = Modal("modal-settings")
		modal.title(`<i class="far fa-cog"></i> Settings`)

		if (Theme.isDark()) modal.dark()

		modal.onShown(() => {
			let modalDiv = $('#modal-settings-jst')
			modalDiv.css('box-shadow', '1px 1px 2px black')
   
            fontSizeDom.focus()
   
		})
		modal.show({w: 520, pad: 0, showOverlay: false})
	}

	function saveWinSizePref () {
		let fw = DivResizer.pxToPercent($(fileSecDom).width())
		let cw = DivResizer.pxToPercent($(codeSecDom).width())
		let rw = DivResizer.pxToPercent($(resultSecDom).width())

		if (fw !== 0) {
			Biscuit.set('fw', fw)
		}

		Biscuit.set('cw', cw)
		Biscuit.set('rw', rw)

		// TODO - not sure about this!
		editor.resize()
	}

	function toggleFileSec () {
		if (fwAnimating) return
		fwAnimating = !fwAnimating

		// Hiding now, so save the WinPref!
		if (fwShown) {
			saveWinSizePref()
		}

		// Toggle the state, it is going to!
		Biscuit.set('file-sec-hidden', fwShown)

		let w = fwShown ? 0 : Biscuit.getFloat('fw', 25)

		$(bar1).toggle('fade', 500)
		$(fileSecDom).animate({
			width: `${w}%`,
		}, 500, () => {
			fwAnimating = false
			fwShown = !fwShown
		})
	}

	function toggleHatiDoc () {
		
		if (OverlayManager.isShowing('hati-doc-modal')) {
			dismissPopup('hati-doc-modal')
            return
        }
		
        let modal = Modal('hati-doc-modal')
            .title(`<i class="fal fa-book-spells"></i> Hati Docs 🐘`)
        if (Theme.isDark()) modal.dark()
        
        let w = DivResizer.percentToPx(95)
        let h = DivResizer.percentToPx(50)
        modal.show({w: w, h: h, pad: 0})
    }

	function toggleShortcut () {
		let modal = Modal('modal-shortcuts')
		modal.setTitle(`<i class="fas fa-keyboard"></i> Shortcuts`)
		if (Theme.isDark()) modal.dark()
		modal.show({w: 520})
	}
	
	function setMsg (msg, status, timeout = 3) {
		let bg, color, icon
  
		if (status === 1) {
			icon = iconDone;
		    color = '#0f5132'
            bg = '#d1e7dd'
		} else if (status === -1) {
			icon = iconError
			color = '#842029'
            bg = '#f8d7da'
		} else if (status === 0) {
			icon = iconInfo
		    color = '#052c65'
            bg = '#CFE2FF'
		} else {
			icon = iconWarning
		    color = '#664d03'
            bg = '#fff3cd'
		}
		
        $(msgDom).html(`<span id="header-msg" style="background-color: ${bg}; color: ${color}">${icon} &nbsp;${msg}</span>`)
		$(msgDom).fadeIn(250)
		
		if (timeout === -1) return
		
		jst.runLater(timeout, () => {
			$(msgDom).fadeOut(250)
		})
	}
	
	function fileSavedErrDialog (filename) {
		let d = Dialog('failed')
		if (Theme.isDark()) d.dark()

		d.title('Error')
		d.msg(`<span class="jst-txt-sm">Failed to save changes for ${filename}!<br>Please make sure you have backed it up somewhere before continuing.</span>`)
		d.acknowledge(() => d.dismiss())
        
        d.show()
    }
    
	function dLog (msg) {
	    if (!debug) return
        log(msg)
    }
    
	function increaseFontSize () {
		let size = parseInt(editor.getFontSize(), 10) || defFontSize
		size = Math.min(size + 1, defMaxFontSize)
		setFontSize(size)
    }
    
    function decreaseFontSize () {
		let size = parseInt(editor.getFontSize(), 10) || defFontSize
		size = Math.max(size - 1, defMinFontSize)
		setFontSize(size)
    }
    
	function clearOutput () {
		outputContentDom.empty()
		setMsg('Output cleared', 0)
    }

	function showTippyMsg(msg, ele, placement = 'top', duration = 1000)	{
		tippy(ele, {
			animation: 'scale',
			content: msg,
			trigger: "manual",
			placement: placement,
			onShow(instance) {
				setTimeout(() => instance.destroy(), duration)
			}
		}).show();
	}
	
    function exeCounter (time) {
        let min = Num.lead0(Math.floor(time / 60))
        let sec = Num.lead0(time % 60)

        countDown.html(`${min}:${sec}`)
    }
	
	function showTimer () {
		
		if (timer !== null) {
			clearInterval(timer)
        }
		
		
		
		let time = countdownTime
        
        exeCounter(time)
		$(executeInfo).fadeIn(250)
		
  
		timer = setInterval(() => {
			time --
   
			if (time <= 0) {
                stopTimer()
                return
			}
			
			exeCounter(time)
		}, 1000)
    }
	
	function stopTimer () {
		clearInterval(timer)
		$(executeInfo).fadeOut(250)
    }
	
	jst.run(() => {

		initUITheme()
		initWindowPref()
        initTippy()
		
		listenToFontChanges()
		listenToShowWhitespaceChange()
        listenToFileDelConfirmChange()
		listenToTabSizeChange()
        
        initAce()
		initFont(editor)
		initTheme(editor)
        
        listenToAceEvents()
        
        /*
         * TODO - strange thing!
         * */
        executeInfo.hide()
        
        /*
		 * Are you ready? ;)
         * */
		jst.runLater(0, () => {
			$('#pac-man').fadeOut(250)
		})
    
    })
    
</script>
</body>
</html>