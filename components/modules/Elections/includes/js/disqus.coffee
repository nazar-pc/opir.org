###*
 * @package        Elections
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
###

initialized = false
window.init_disqus = (disqus_identifier)->
	if !initialized
		window.disqus_identifier	= disqus_identifier
		window.disqus_url			= 'https://opir.org/#!' + disqus_identifier
		dsq							= document.createElement('script')
		dsq.type					= 'text/javascript'
		dsq.async					= true
		dsq.src						= '//' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq)
		initialized = true
	else
		DISQUS.reset
			reload	: true
			config	: ->
				this.page.identifier	= disqus_identifier
				this.page.url			= 'https://opir.org/#!' + disqus_identifier
