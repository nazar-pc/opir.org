$ ->
	if cs.module != 'Elections'
		return
	$(document).on(
		'click'
		'.cs-elections-social-links > *'
		->
			$this	= $(@)
			parent	= $this.parent()
			violation = parent.data('violation')
			title	= $('title').text()
			content	= violation.text
			link	= $('base').attr('href') + "Elections/violation/#{violation.precinct}/#{violation.id}"
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
				window.open('https://twitter.com/intent/tweet?status=' + link + ' ' + content + ' via @OpirOrg', 'share_opir.org', params)
	)
