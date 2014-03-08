$ ->
	if cs.module != 'Streams'
		return
	$(document).on(
		'click'
		'.cs-streams-social-links > *'
		->
			$this	= $(@)
			parent	= $this.parent()
			id		= parent.data('id')
			link	= $('base').attr('href') + 'Streams/' + id
			params	= 'location=no,width=500,height=400,resizable=no,status=no'
			if $this.hasClass('vk')
				window.open('https://vk.com/share.php?url=' + link, 'share_opir.org', params)
			else if $this.hasClass('fb')
				window.open('https://www.facebook.com/sharer/sharer.php?src=sp&u=' + link, 'share_opir.org', params)
			else if $this.hasClass('tw')
				window.open('https://twitter.com/intent/tweet?status=' + link, 'share_opir.org', params)
	)
