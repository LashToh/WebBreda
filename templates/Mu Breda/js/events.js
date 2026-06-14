$(function() {
	loadEventSchedule();
	if (typeof serverTime !== 'undefined' && $('#tServerTime').length) {
		serverTime.init('tServerTime', 'tLocalTime', 'tServerDate', 'tLocalDate');
	}
});

var serverTime = {
	weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
	monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	serverDate: null,
	localDate: null,
	dateOffset: null,
	nowDate: null,
	eleServer: null,
	eleLocal: null,
	eleServerDate: null,
	eleLocalDate: null,
	init: function(e, c, s, l) {
		var f = this;
		f.eleServer = e;
		f.eleLocal = c;
		f.eleServerDate = s;
		f.eleLocalDate = l;
		$.getJSON(baseUrl + 'api/servertime.php', function(a) {
			f.serverDate = new Date(a.ServerTime.replace(/\//g, '-'));
			f.localDate = new Date();
			f.dateOffset = f.serverDate - f.localDate;
			document.getElementById(f.eleServer).innerHTML = f.dateTimeFormat(f.serverDate);
			document.getElementById(f.eleLocal).innerHTML = f.dateTimeFormat(f.localDate);
			document.getElementById(f.eleServerDate).innerHTML = f.dateFormat(f.serverDate);
			document.getElementById(f.eleLocalDate).innerHTML = f.dateFormat(f.localDate);
			setInterval(function() { f.update(); }, 1000);
		});
	},
	update: function() {
		var b = this;
		b.nowDate = new Date();
		document.getElementById(b.eleLocal).innerHTML = b.dateTimeFormat(b.nowDate);
		b.nowDate.setTime(b.nowDate.getTime() + b.dateOffset);
		document.getElementById(b.eleServer).innerHTML = b.dateTimeFormat(b.nowDate);
	},
	dateTimeFormat: function(e) {
		var c = this;
		return c.digit(e.getHours()) + ':' + c.digit(e.getMinutes()) + ':' + c.digit(e.getSeconds());
	},
	dateFormat: function(e) {
		var c = this;
		return c.weekDays[e.getDay()] + ' ' + c.monthNames[e.getMonth()] + ' ' + e.getDate();
	},
	digit: function(b) {
		b = String(b);
		return b.length === 1 ? '0' + b : b;
	}
};

function loadEventSchedule() {
	$.getJSON(baseUrl + "api/events.php", function(data) {
		$.each(data, function(key, val) {
			if($('#' + key).length) {
				eventSchedule(key, val.opentime, val.duration, val.offset, val.timeleft);
				document.getElementById(key + '_name').innerHTML = val.event;
				document.getElementById(key + '_next').innerHTML = val.nextF;
			}
		})
	})
}

function eventSchedule(eventId, openTime, duration, offset, timeLeft) {
	var eHours = null;
	var eMinutes = null;
	var eSeconds = null;
	
	function init() {
		setInterval(function() {
			update();
		}, 1000)
	}
	
	function reloadEventInfo() {
		$.getJSON(baseUrl + "api/events.php?event=" + eventId, function(data) {
			openTime = data.opentime;
			duration = data.duration;
			offset = data.offset;
			timeLeft = data.timeleft;
			document.getElementById(eventId + '_name').innerHTML = data.event;
			document.getElementById(eventId + '_next').innerHTML = data.nextF;
		})
	}
	
	function update() {
		if(timeLeft >= 1) {
			
			var days_module = timeLeft % 86400;
			eDays = (timeLeft-days_module)/86400;
			var hours_module = days_module % 3600;
			eHours = (days_module-hours_module)/3600;
			var minutes_module = hours_module % 60;
			eMinutes = (hours_module-minutes_module)/60;
			eSeconds = minutes_module;
			
			if(eMinutes < 10) eMinutes = '0' + eMinutes;
			if(eSeconds < 10) eSeconds = '0' + eSeconds;
		} else {
			eDays = '0';
			eHours = '0';
			eMinutes = '00';
			eSeconds = '00';
			
			reloadEventInfo();
		}
		
		if(openTime > 0) {
			if(offset-timeLeft < openTime) {
				document.getElementById(eventId).innerHTML = '<span class="event-schedule-open">Open</span>';
				timeLeft = timeLeft-1;
				return;
			}
		} else {
			if(duration > 0) {
				if(offset-timeLeft < duration) {
					document.getElementById(eventId).innerHTML = '<span class="event-schedule-inprogress">In Progress</span>';
					timeLeft = timeLeft-1;
					return;
				}
			}
		}
		
		if(eHours == '00' && eMinutes == '00') {
			document.getElementById(eventId).innerHTML = eSeconds + " sec";
		} else {
			if(eDays > 0) {
				document.getElementById(eventId).innerHTML = eDays + " days " + eHours + " hrs " + eMinutes + " min " + eSeconds + " sec";
			} else {
				document.getElementById(eventId).innerHTML = eHours + " hrs " + eMinutes + " min " + eSeconds + " sec";
			}
		}
		
		timeLeft = timeLeft-1;
	}
	
	init();
};