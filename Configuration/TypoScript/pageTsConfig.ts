# Set preview text height
RTE.config.tx_t3blog_post.preview_text.RTEHeightOverride = 300

# Link validator config
mod.linkvalidator.searchFields {
	tx_t3blog_cat = description
	tx_t3blog_com = text, website
	tx_t3blog_post = preview_text, trackback
	tx_t3blog_pingback = url
	tx_t3blog_trackback = fromurl
}