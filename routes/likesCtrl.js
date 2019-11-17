// Imports
var models = require('../models');
var asyncLib = require('async');
var jwtUtils = require('../utils/jwt.utils');

// Constants
const TITLE_LIMIT = 2;
const CONTENT_LIMIT = 4;
const ITEMS_LIMIT = 50;

// Routes
module.exports = {
  createLikes: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var ID_PHOTO = req.body.ID_PHOTO;
    


    if (ID_PHOTO == null) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }


    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          where: { ID_MEMBRE: ID_MEMBRE}
        })
          .then(function (userFound) {
            done(null, userFound);
          })
          .catch(function (err) {
            console.log(err);
            return res.status(500).json({ 'error': 'unable to verify user' });
          });
      },
      function (userFound, done) {
        if (userFound) {
          models.likes.create({
            ID_MEMBRE: ID_MEMBRE,
            ID_PHOTO: ID_PHOTO
          })
            .then(function (newLikes) {
              done(newLikes);
            });
        } else {
          res.status(404).json({ 'error': 'user not found' });
        }
      },
    ], function (newLikes) {
      if (newLikes) {
        return res.status(201).json(newLikes);
      } else {
        return res.status(500).json({ 'error': 'cannot post like' });
      }
    });

  },
  nbLike: function (req, res) {
    var fields = req.query.fields;
    var limit = parseInt(req.query.limit);
    var offset = parseInt(req.query.offset);
    var order = req.query.order;

    var ID_PHOTO = req.body.ID_PHOTO;

    models.likes.findAndCountAll ({
      where: {ID_PHOTO: ID_PHOTO},
      /*order: [(order != null) ? order.split(':') : ['ID_PHOTO', 'ASC']],
      attributes: (fields !== '*' && fields != null) ? fields.split(',') : null,
      limit: (!isNaN(limit)) ? limit : null,
      offset: (!isNaN(offset)) ? offset : null,*/
      /*include: [{
        model: models.membre,
        attributes: [ 'MAIL' ]
      }]*/
    }).then(function (commentaire) {
      if (commentaire) {
        res.status(200).json(commentaire);
      } else {
        res.status(404).json({ "error": "no events found" });
      }
    }).catch(function (err) {
      console.log(err);
      res.status(500).json({ "error": "invalid fields" });
    });
  },}