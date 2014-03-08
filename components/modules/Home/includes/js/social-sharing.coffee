$ ->
	if cs.module != 'Home'
		return
	$(document).on(
		'click'
		'.cs-home-social-links > *'
		->
			$this	= $(@)
			parent	= $this.parent()
			id		= parent.data('id')
			title	= parent.parent().find('h2:first').text()
			content	= parent.prev().text().replace(/\n/g, ' ')
			content	= content.replace(/\t/g, '')
			link	= $('base').attr('href') + id
			params	= 'location=no,width=500,height=400,resizable=no,status=no'
			image	= parent.parent().find('img:first')
			image	=
				if image.length
					image.prop('src')
				else
					''
			if $this.hasClass('vk')
				window.open('https://vk.com/share.php?url=' + link + '&title=' + title + '&description=' + content + (if image then '&image=' + image else ''), 'share_opir.org', params)
			else if $this.hasClass('fb')
				window.open('https://www.facebook.com/sharer/sharer.php?src=sp&u=' + link + '&t=' + title + '&description=' + content + (if image then '&image=' + image else ''), 'share_opir.org', params)
			else if $this.hasClass('tw')
				window.open('https://twitter.com/intent/tweet?status=' + link + ' ' + title + ' ' + content, 'share_opir.org', params)
	)
