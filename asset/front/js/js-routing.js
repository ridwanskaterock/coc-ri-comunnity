$(function(){

  var app = new Davis(function () {
    this.get('/', function () {
      // do something when url was changed to '/'
    })
     this.get('/base', function () {
      // do something when url was changed to '/'
    })
    this.get('/user/:name', function (req) {
      // do something when url was changed to '/user/*'
      var name = req.params['name']; // you can get the value of ':name' in url
    })
    this.get('/help', function (req) {
      // do something when url was changed to '/help'
    })
    this.post('/update', function (req) {
      // do something when url was changed to '/update'
    })
  })
  app.bind('routeNotFound', function(req){
    // if the url was not routed, this event handler will be fired.
    alert('this url was not routed!');
  });

});
