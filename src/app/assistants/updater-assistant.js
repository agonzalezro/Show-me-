function UpdaterAssistant() { }

UpdaterAssistant.prototype.setup = function() {
    this.cookie = new Mojo.Model.Cookie('id');
    
    if (this.cookie.get() == null) {
        //Get a random free number
        var request = new Ajax.Request("http://becado.es/?action=new", {
            method: 'get',
            evalJSON: false,
            frequency: 1,
            onSuccess: this.ajaxRequestNewSuccess.bind(this)
        });
    } else {
        $("url").innerHTML = "<a href='http://becado.es/" + this.cookie.get() + "'>http://becado.es/" + this.cookie.get() + "</a>";
        this.identifier = this.cookie.get();
    }
    
    setInterval(getCurrentPosition.bind(this), 1000);
    
    this.controller.setupWidget("maps",
        this.attributes = { },
        this.model = {
            label : "Show me in maps!",
            disabled: true
        }
     );
        
    Mojo.Event.listen(this.controller.get("maps"), Mojo.Event.tap, this.handleUpdate.bind(this));

};

function getCurrentPosition() {
    this.controller.serviceRequest('palm://com.palm.location', {
        method : 'getCurrentPosition',
        parameters: {
            responseTime: 2,
            subscribe: false
                },  
        onSuccess: this.handleServiceResponse.bind(this),
        onFailure: this.handleServiceResponseError.bind(this)
    });
}

UpdaterAssistant.prototype.ajaxRequestNewSuccess = function(event) {
    this.identifier = parseInt(event.responseText);
    
    if (this.identifier == null) {
        this.cookie.put("1");
        this.identifier = this.cookie.get();
    }
    else {
        this.cookie.put(this.identifier + 1);
        this.identifier = this.cookie.get();
    }
    
    $("url").innerHTML = "<a href='http://becado.es/" + this.identifier + "'>http://becado.es/" + this.identifier + "</a>";
}

UpdaterAssistant.prototype.handleUpdate = function(event) {
    this.controller.serviceRequest('palm://com.palm.applicationManager', {
     	method: 'launch',
     	parameters: {
     		id:"com.palm.app.maps",
            layer:"t",
     		params:{"query":this.latitude + "," + this.longitude}
     	}
    }); 
}

UpdaterAssistant.prototype.handleServiceResponse = function(event) {
    if (this.model.disabled == true) {
        this.model.disabled = false;
        this.controller.modelChanged(this.model, this.controller.get("maps"));
    }
    
    this.latitude = event.latitude;
    this.longitude = event.longitude;
    if (this.identifier != null) {
        new Ajax.Request("http://becado.es/index.php?action=post&id=" + this.identifier + "&lat=" + this.latitude + "&lng=" + this.longitude);
    }
    
    //$('messages').innerHTML = $('messages').innerHTML + "<br />" + event.latitude + ":" + event.longitude;
    $('latitude').innerHTML = this.latitude;
    $('longitude').innerHTML = this.longitude;
}

UpdaterAssistant.prototype.handleServiceResponseError = function(event) {
    Mojo.Controller.errorDialog("Imposible conseguir tu posición GPS.");
}