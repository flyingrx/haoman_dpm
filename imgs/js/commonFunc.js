function countUp(elem, endVal, startVal, duration, decimal) {
	//传入参数依次为 数字要变化的元素，最终显示数字，数字变化开始值，变化持续时间，小数点位数
	var startTime = 0;
	var dec = Math.pow(10, decimal);
	var progress,value;
	function startCount(timestamp) {
		if(!startTime) startTime = timestamp;
		progress = timestamp - startTime;
		value = startVal + (endVal - startVal) * (progress / duration);
		value = (value > endVal) ? endVal : value;
		value = Math.floor(value*dec) / dec;
		elem.innerHTML = value.toFixed(decimal);
		progress < duration && requestAnimationFrame(startCount)
	}
	requestAnimationFrame(startCount)
}