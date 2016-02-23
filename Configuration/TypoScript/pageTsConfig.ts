# Set preview text height
RTE.config.tx_t3blog_post.preview_text.RTEHeightOverride = 300

TCEFORM.tx_t3blog_post {
	# See https://github.com/fnagel/t3extblog/issues/22
	number_views.disabled = 1
}

# Link validator config
mod.linkvalidator.searchFields {
	tx_t3blog_cat = description
	tx_t3blog_com = text, website
	tx_t3blog_post = preview_text, trackback
	tx_t3blog_pingback = url
	tx_t3blog_trackback = fromurl
}

# Hide tt_content elements in list view
[page|module = t3blog]
	mod.web_list.table.tt_content.hideTable = 1
[global]