function Timer(seconds) {
	this.current = -1;
	this.max = -1;
	this.interval = seconds;
	this.time = seconds;
	this.timer = null;
	this.comics = [];

	$(window).on('hashchange', this.gotoComicHandler.bind(this));
	$('*').on('click keyup mousemove', this.stopScrolling.bind(this));
}
Timer.prototype.start = function () {
	this.time = this.interval;
	this.trigger();
	this.resume();
};
Timer.prototype.hidePlay = function () {
	$('#play').attr('disabled', true);
	$('#play').css('display', 'none');
	$('#pause').attr('disabled', false);
	$('#pause').css('display', 'inline-block');
};
Timer.prototype.showPlay = function () {
	$('#play').attr('disabled', false);
	$('#play').css('display', 'inline-block');
	$('#pause').attr('disabled', true);
	$('#pause').css('display', 'none');
};
Timer.prototype.hidePrev = function () {
	$('#prev').attr('disabled', true);
};
Timer.prototype.showPrev = function () {
	$('#prev').attr('disabled', false);
};
Timer.prototype.hideNext = function () {
	$('#next').attr('disabled', true);
};
Timer.prototype.showNext = function () {
	$('#next').attr('disabled', false);
};
Timer.prototype.hideCounter = function () {
	$('#counter_holder').css('display', 'none');
};
Timer.prototype.showCounter = function () {
	$('#counter_holder').css('display', 'inline-block');
};
Timer.prototype.tick = function () {
	this.update();
	this.time--;
	if (this.time <= 0) {
		this.time = this.interval;
		this.trigger();
	} else if (this.time >= this.interval) {
		this.time = this.interval;
	}
};
Timer.prototype.updateInterval = function(interval) {
	this.interval = interval;
	if (this.time > interval) {
		this.time = interval;
	}
	if ($('#interval').val() != interval) {
		$('#interval').val(interval);
	}
}
Timer.prototype.running = function () { return this.timer != null; };
Timer.prototype.stopped = function () { return this.timer == null; };
Timer.prototype.resume = function () {
	var sources = this.enabledSources();
	if (sources.length > 0) {
		this.hidePlay();
		this.showCounter();
		this.showNext();
		clearInterval(this.timer);
		this.timer = setInterval(this.tick.bind(this), 1000);
	} else {
		alert("No comics selected");
	}
};
Timer.prototype.stop = function () {
	this.showPlay();
	this.hideCounter();

	clearInterval(this.timer);
	this.timer = null;
};
Timer.prototype.stopScrolling = function () {
	$('.comic-panel').stop();
}
Timer.prototype.gotoComicHandler = function (e) { this.gotoComic(); }
Timer.prototype.gotoComic = function () {
	if (window.location.hash == '#') {
		return;
	}

	var source = this.getHashValue('src');
	var slug = this.getHashValue('slug');
	for (var c in this.comics) {
		if (this.comics[c].source == source && this.comics[c].slug == slug) {
			if (c < (this.comics.length-1)) {
				this.stop();
			}
			this.display(this.comics[c]);
			return;
		}
	}

	this.getComic();
}
Timer.prototype.updateHash = function () {
	var hash = '#src=' + this.comics[this.current].source
		+ '&slug=' + this.comics[this.current].slug;
	if (window.location.hash != hash) {
		window.location.hash = hash;
	}
};
Timer.prototype.prev = function () {
	this.stop();
	this.stopScrolling();

	if (this.current >= 0) {
		this.current--;
	}
	if (this.current == 0) {
		this.hidePrev();
	}
	
	this.updateHash();
	this.display(this.comics[this.current]);
};
Timer.prototype.next = function () {
	this.stop();
	this.stopScrolling();

	if (this.current < this.max) {
		this.current++;
		this.updateHash();
	} else {
		this.start();
	}
};
Timer.prototype.update = function () { $('#counter').html(this.time); };
Timer.prototype.addComic = function (data) {
	this.comics.push(data);
	this.max++;
	this.current = this.max;
	this.updateHash();
	this.display(this.comics[this.comics.length-1]);
	this.showNext();
	this.resume();

	$('#comics').prepend(
		'<li>'
			+ '<a href="#src=' + data.source + '&slug=' + data.slug + '">'
			+ '<div>' + data.source + ' ' + data.title + '</div>'
			+ '<img src="' + data.src + '" />'
		+ '</a></li>'
	);
};
Timer.prototype.display = function (comic) {
	this.stopScrolling();
	$('.comic-panel').scrollTop(0);

	$('#name').html(comic.title);
	$('#source').html(comic.source_title);
	$('#link').attr('href', comic.link);
	$('#serial').html(comic.serial);
	$('#flavor').html(comic.alt);
	$('#img').attr('src', comic.src);
	$('#img').off('load');
	$('#img').on('load', (function () {
		var panel = $('.comic-panel');
		var scrollMax = panel[0].scrollHeight - panel.height();
		if (this.running()) {
			// autoscroll the comic
			panel.animate({scrollTop: scrollMax}, {duration: (this.interval * 500)});

		}
	}).bind(this));

	if (this.current > 0) {
		this.showPrev();
	} else {
		this.hidePrev();
	}
}
Timer.prototype.comicEnabled = function (comic) {
	return $('#ok-' + comic).prop('checked');
}
Timer.prototype.enabledSources = function () {
	var sources = [
		'xkcd',
		'qwantz',
		'smbc',
		'awkwardyeti',
	];
	return sources.filter(this.comicEnabled);
}
Timer.prototype.getHashValue = function (key) {
	var matches = location.hash.match(new RegExp(key+'=([^&]*)'));
	return matches ? matches[1] : null;
}
Timer.prototype.getComic = function () {
	this.hideNext();
	this.stop();
	var sources = this.enabledSources();

	var source;
	var src = this.getHashValue('src');
	var slug = this.getHashValue('slug');
	if (src && slug && sources.includes(src)) {
		source = src;
	} else if (sources.length > 0) {
		source = sources[Math.floor(Math.random()*sources.length)];
	}
	slug = slug ? ('?slug=' + slug) : '';
	
	var url = source + '.php' + slug;
	$.get(source + '.php' + slug, this.addComic.bind(this));
}

Timer.prototype.trigger = function () {
	window.location.hash = '#';
}
