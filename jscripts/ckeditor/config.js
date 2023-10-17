/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.extraPlugins = 'pbckcode';
	config.pbckcode = {
		modes : [["PHP","php"],["Java","java"],["C#","csharp"],["C++","cpp"],["CSS","css"],["Delphi","delphi"],["JavaScript","js"],["Perl","perl"],["Python","python"],["Ruby","ruby"],["SQL","sql"],["VB","vb"],["XML","xml"]],
		highlighter : "SYNTAX_HIGHLIGHTER"
	};
};
