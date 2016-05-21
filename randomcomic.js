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

	// get at least one comic
	this.trigger();

	this.resume();
}
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
};
Timer.prototype.display = function (comic) {
	$('#name').html(comic.alt);
	$('#flavor').html(comic.title);
	$('#img').attr('src', comic.src);
}
Timer.prototype.getComic = function () {
	$.get('xkcd.php', this.addComic.bind(this));
}
Timer.prototype.trigger = function () {
	this.getComic();
}
