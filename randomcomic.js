function Timer(seconds) {
	this.current = -1;
	this.max = -1;
	this.interval = seconds;
	this.time = seconds;
	this.timer = null;
	this.comics = [];
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
Timer.prototype.resume = function () {
	this.hidePlay();

	// every second, run update with new time
	var intervalFunction = (function () {
		this.update();
		this.time--;
		if (this.time <= 0) {
			this.time = this.interval;
			this.trigger();
		}
	}).bind(this);
	this.timer = setInterval(intervalFunction, 1000);
};
Timer.prototype.stop = function () {
	this.showPlay();

	clearInterval(this.timer);
};
Timer.prototype.prev = function () {
	this.stop();

	if (this.current >= 0) {
		this.current--;
	}
	if (this.current == 0) {
		this.hidePrev();
	}
	
	this.display(this.comics[this.current]);
};
Timer.prototype.next = function () {
	this.stop();

	if (this.current < this.max) {
		this.current++;
	} else {
		this.start();
	}
	this.display(this.comics[this.current]);
};
Timer.prototype.update = function () { $('#counter').html(this.time); };
Timer.prototype.addComic = function (data) {
	this.comics.push(data);
	this.max++;
	this.current = this.max;
	this.display(this.comics[this.comics.length-1]);
	this.showNext();
};
Timer.prototype.display = function (comic) {
	$('#name').html(comic.alt);
	$('#source').html(comic.source);
	$('#link').attr('href', comic.link);
	$('#serial').html(comic.serial);
	$('#flavor').html(comic.title);
	$('#img').attr('src', comic.src);

	if (this.current > 0) {
		this.showPrev();
	} else {
		this.hidePrev();
	}
}
Timer.prototype.getComic = function () {
	this.hideNext();
	$.get('xkcd.php', this.addComic.bind(this));
}
Timer.prototype.trigger = function () {
	this.getComic();
}
