//Imports
var express = require('express');
var usersCtrl = require('./routes/usersCtrl');
var eventsCtrl = require('./routes/eventsCtrl');
var shopCtrl = require('./routes/shopCtrl');
var cartCtrl = require('./routes/cartCtrl');
var likesCtrl = require('./routes/likesCtrl');
var commentCtrl = require('./routes/commentCtrl');

// Router
exports.router = (function() {
    var apiRouter = express.Router();

    // Users routes
    apiRouter.route('/users/register/').post(usersCtrl.register);
    apiRouter.route('/users/login/').post(usersCtrl.login);
    apiRouter.route('/users/me/').get(usersCtrl.getUserProfile);
    apiRouter.route('/users/me/').put(usersCtrl.updateUserProfile);
    apiRouter.route('/users/list/').get(usersCtrl.listUser);

    // Events routes
    apiRouter.route('/events/new/').post(eventsCtrl.createEvents);
    apiRouter.route('/events').get(eventsCtrl.listEvents);
    apiRouter.route('/events/sign').post(eventsCtrl.signEvents);

    // Shop routes
    apiRouter.route('/item/new/').post(shopCtrl.createItem);
    apiRouter.route('/item').get(shopCtrl.listItem);

    // Cart routes
    apiRouter.route('/cart/new/').post(cartCtrl.createCart);
    apiRouter.route('/cart').get(cartCtrl.listCart);

    // Likes routes
    apiRouter.route('/photo/like/').post(likesCtrl.createLikes);
    apiRouter.route('/photo/like/nb/').get(likesCtrl.nbLike);

    // Comments routes
    apiRouter.route('/comments/new/').post(commentCtrl.createComments);
    apiRouter.route('/comments/list/').get(commentCtrl.listComments);


    return apiRouter;
})();