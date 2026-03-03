/**
 * Forum — affiche le bloc pour rédiger une réponse (utilisateur connecté)
 */
var commentShowDOM = $("#blogArticleCommentShow");
if (commentShowDOM.length) {
	commentShowDOM.on("click focus", function() {
		$("#blogArticleCommentWrapper").removeClass("displayNone").hide().fadeIn();
		// Focus sur l’éditeur / textarea
		$("#blogArticleContent").trigger("focus");
	});

	// Si erreurs de validation : ouvrir automatiquement
	if ($("#blogArticleCommentWrapper").find("textarea.notice,input.notice").length) {
		commentShowDOM.trigger("click");
	}

	// Scroll vers la zone de réponse à l’envoi
	$("#blogArticleForm").on("submit", function() {
		$(location).attr("href", "#comment");
	});
}


/**
 * Citer une réponse dans TinyMCE
 */
$(document).on("click", ".forumQuoteBtn", function(e){
	e.preventDefault();
	var author = $(this).data("author") || "";
	var when = $(this).data("when") || "";
	var quote = $(this).data("quote") || "";
	quote = String(quote).trim();
	if (!quote) { return; }

	var html = '<blockquote><p><strong>' + escapeHtml(author) + '</strong> — ' + escapeHtml(when) + '</p><p>' + escapeHtml(quote) + '</p></blockquote><p></p>';

	// Ouvrir le formulaire si besoin
	$("#blogArticleCommentWrapper").removeClass("displayNone").hide().fadeIn();
	$(location).attr("href", "#comment");

	// TinyMCE si présent
	if (typeof tinymce !== "undefined" && tinymce.activeEditor) {
		tinymce.activeEditor.focus();
		tinymce.activeEditor.execCommand("mceInsertContent", false, html);
	} else {
		var ta = $("#blogArticleContent");
		if (ta.length) {
			ta.val((ta.val() || "") + "\n\n" + $("<div>").html(html).text());
			ta.trigger("focus");
		}
	}
});

function escapeHtml(str){
	return String(str)
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
}
