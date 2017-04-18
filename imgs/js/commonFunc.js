function countUp(elem, endVal, startVal, duration, decimal) {
	//�����������Ϊ ����Ҫ�仯��Ԫ�أ�������ʾ���֣����ֱ仯��ʼֵ���仯����ʱ�䣬С����λ��
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