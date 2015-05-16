//angular module for Arcfolio.//
var app = angular.module("Arcfolio", ['ngRoute', 'ui.bootstrap', 'vcRecaptcha']);

/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//configure router - used for sending user to different pages dinamically.//*/
app.config(function($routeProvider, $locationProvider) 
{
	
	
	console.log("router working...");
    $locationProvider.html5Mode(true);
  	$routeProvider
	.when('/arcfolio/index.php', 
	{
	   	templateUrl: 'html_plugins/default.html',
	   	controller: 'testController'
	})
	
	
	.when('/arcfolio/user', 
		{
		templateUrl: 'html_plugins/userInterface.html',
		controller: 'testController2'
  	})
	//send all other pages back to index.
    .otherwise(
	{
		redirectTo: '/arcfolio/index.php'
	});
	
	
			
});


/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
** navbar ** */

app.directive('navbar', function()
{
	return	{
			  restrict: 'E',
			  templateUrl: 'html_plugins/navBar.html'
			};
});


/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
** newsfeed ** */
//will need radical rewrite.
	
app.directive('newsfeed', function()
{
	return	{
		  		restrict: 'E',
		  		controller: ['$scope', function($scope)
				{
					  $scope.myInterval = 4000;
					  var slides = $scope.slides = [];
					  
					  $scope.addSlide = function() {
						var newWidth = 600 + slides.length + 1;
						slides.push({
						  image: 'http://placekitten.com/' + newWidth + '/300',
						  text: ['Serious Design work needed.','Angular functionality works tho.','so now its just kittens.','Even though Im a dog person.'][slides.length % 4] + ' ' +
							['', 'Kittys', 'Felines', 'Cutes'][slides.length % 4]
						});
					  };
					  for (var i=0; i<4; i++) {
						$scope.addSlide();
					  }
				}],
				templateUrl: 'html_plugins/newsfeed.html'
			};

});


/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
** registerController ** */
app.controller('registerController', function($scope, $http, $rootScope) {
	
	$scope.user = null;
	
	//this hides the re-enter password input in default.html//
	$scope.registerBool = false;
	$scope.response = null;
	
	
	//this toggles the re-enter password input.
	$scope.register = function() {
		console.log("clicked");
		if($scope.registerBool == false)
		{ $scope.registerBool = true; }
		else if($scope.registerBool == true)
		{ $scope.registerBool = false }
		};
		
		
	$scope.createAccount = function(data)
		{
			console.log("createaccount clicked");
		  //configure get data to send to php//
		  var config = {
			params: {
			  accountData : data
			}
		  };
		  
		  //loading animation goes here//
		  
		  $http.post("php_plugins/createAccount.php", null, config)
			.success(function (data, status, headers, config)
			{
			  //finish animation process here//
			  
			  //create global array with response data//
			  $rootScope.user = data;
			  
			  //check if response data shows a backend failure.//
			  if($rootScope.user.failure == true || $rootScope.user == null)
			  {
				  //process backend failure//
				  	console.log("ERROR://///////////");
					console.log($rootScope.user);
					
					$rootScope.user = null;
					
					$scope.response = "ERROR: " + data.msg;
					$scope.responsecolor = "red";
					$scope.responsedisable = "";
			  }
			  //response data is without backend failure//
			  else if($rootScope.user.failure == false)
			  {
				  //process correct data//
				  	console.log("SUCESSFUL ACCOUNT CREATION");
					console.log($rootScope.user);
					$scope.response = "SUCCESS: " + data.msg;
					$scope.responsecolor = "green";
					$scope.responsedisable = "disabled";
			  }
			  else
			  {
				  console.log(data);}
			  
			})
			//there was an error sending the data to php.//
			.error(function (data, status, headers, config)
			{
					console.log("CONNECTIVITY ERROR");
			});
		};
		///////////////////////////////////////////////////////////////////////////////////////////////////
		
	
});


//these two are test controllers.
app.controller('testController', function($scope) {
	
	$scope.test = 'hello world';
	
});

app.controller('testController2', function($scope) {
	
	$scope.test = 'hello world';
	
});