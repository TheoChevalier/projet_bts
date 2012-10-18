var uploadButton = {
  addEvent: function (el, event, function_) {
    if (el.addEventListener) {
      el.addEventListener(event, function_, false);
    } else if (el.attachEvent) {
      el.attachEvent('on'+ event, function_);
    }
  },
  
  mousePos: function (el, e) {
    var pos = this.realPos(el);
    if(window.event) { e = window.event }
    
    return {
      left: e.clientX - pos.left,
      top: e.clientY - pos.top
    };
  },

  realPos: function (el) {
    var top = el.offsetTop;
    var left = el.offsetLeft;
    
    while(el = el.offsetParent) {
      top += el.offsetTop;
      left += el.offsetLeft;
    }
    
    return {
      left: left,
      top: top
    };
  },
  
  getScroll: function() {
    var xScroll, yScroll = 0;
    
    if(self.pageXOffset || self.pageYOffset) {
      xScroll = self.pageXOffset;
      yScroll = self.pageYOffset;
    } else if (document.documentElement || document.documentElement.scrollTop){
      xScroll = document.documentElement.scrollLeft;
      yScroll = document.documentElement.scrollTop;
    } else if (document.body) {
      xScroll = document.body.scrollLeft;
      yScroll = document.body.scrollTop;
    }
    
    return {left:xScroll, top:yScroll};
  },
  
  id: 0,
  
  storage: [],

  create: function (s) {
    var objRef = this;
    this.id++;
    
    var settings = {
      text: 'Parcourir',
      name: 'file',
      className: '',
      hoverClassName: '',
      disabledClassName: '',
      uploadWhenChanged: false,
      onchange: null
    };
    
    for(i in s) { settings[i] = s[i] }
    
    this.storage[this.id] = {
      className: settings.className,
      disabledClassName: settings.disabledClassName
    };
    
    var globalDiv = document.createElement('div');
      globalDiv.className = settings.className;
    
    var secondDiv = document.createElement('div');
      secondDiv.className = 'uploadButton_'+ this.id; // Dans l'attribut class pour ne pas créer un id inutile.
      secondDiv.style.overflow = 'hidden';
      secondDiv.style.position = 'relative';
      secondDiv.style.width = '100%';
      secondDiv.style.height = '100%';
    globalDiv.appendChild(secondDiv);
    
    secondDiv.innerHTML = settings.text;
    
    var inputFile = document.createElement('input');
      inputFile.type = 'file';
      inputFile.name = settings.name;
      inputFile.style.position = 'absolute';
      inputFile.style.opacity = '0';
      inputFile.style.filter = 'alpha(opacity=0)';
    secondDiv.appendChild(inputFile);
    
    this.addEvent(secondDiv, 'mousemove', function(e) {
      if(objRef.disabled(globalDiv)) { return; }
      
      var inputFile = secondDiv.lastChild;
      var pos = objRef.mousePos(secondDiv, e);
      
      inputFile.style.top = pos.top - 10 + objRef.getScroll().top + 'px';
      inputFile.style.right = secondDiv.offsetWidth + objRef.getScroll().left - (pos.left + 10) + 'px';
    });
    
    this.addEvent(secondDiv, 'mouseover', function() {
      if(objRef.disabled(globalDiv)) { return; }
      globalDiv.className += ' '+ settings.hoverClassName;
    });
    
    this.addEvent(secondDiv, 'mouseout', function() {
      if(objRef.disabled(globalDiv)) { return; }
      globalDiv.className = settings.className;
    });
    
    this.addEvent(inputFile, 'change', function() {
      if(typeof settings.onchange == 'function') {
        settings.onchange(inputFile.value);
      }
      
      if(settings.uploadWhenChanged) {
        var el = inputFile;
        
        while(el.nodeName != 'FORM') {
          el = el.parentNode;
          if(el == null) { return; }
        }
        
        el.submit();
      }
    });
    
    return globalDiv;
  },
  
  disabled: function (button, disabled) {
    if(typeof button != 'object') { return; }
    
    var id = parseInt(button.firstChild.className.split('_')[1]);
    var className = this.storage[id].className;
    var disabledClassName = this.storage[id].disabledClassName;
    
    if(typeof disabled == 'undefined') {
      return (disabledClassName.length > 0 && button.className.indexOf(disabledClassName) != -1)
        ? true : false;
    }
    else if(disabled) {
      button.firstChild.lastChild.style.top = '1500px';
      button.className = className +' '+ disabledClassName;
    } else {
      button.className = className;
    }
  }
};