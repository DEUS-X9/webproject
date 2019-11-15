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
  createCart: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var ID_ITEM = req.body.ID_ITEM;
    var DATE = new Date();
    var NOMBRE = req.body.NOMBRE;


    if (ID_ITEM == null || NOMBRE == null) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          where: { ID_MEMBRE: ID_MEMBRE }
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
          models.panier.create({
            DATE: DATE,
            NOMBRE: NOMBRE,
            ID_ITEM: ID_ITEM,
            ID_MEMBRE : ID_MEMBRE
          })
            .then(function (newCart) {
              done(newCart);
            });
        } else {
            console.log(err);
          res.status(404).json({ 'error': 'user not found' });
        }
      },
    ], function (newCart) {
      if (newCart) {
        return res.status(201).json(newCart);
      } else {
        return res.status(500).json({ 'error': 'cannot put in cart' });
      }
    });

  },
  listCart: function (req, res) {
    var fields = req.query.fields;
    var limit = parseInt(req.query.limit);
    var offset = parseInt(req.query.offset);
    var order = req.query.order;

    if (limit > ITEMS_LIMIT) {
      limit = ITEMS_LIMIT;
    }

    models.panier.findAll({
      order: [(order != null) ? order.split(':') : ['ID_PANIER', 'ASC']],
      attributes: (fields !== '*' && fields != null) ? fields.split(',') : null,
      limit: (!isNaN(limit)) ? limit : null,
      offset: (!isNaN(offset)) ? offset : null,
      /*include: [{
        model: models.panier,
        attributes: [ 'ID_MEMBRE' ]
      }]*/
    }).then(function (cart) {
      if (cart) {
        res.status(200).json(cart);
      } else {
        res.status(404).json({ "error": "no item found" });
      }
    }).catch(function (err) {
      console.log(err);
      res.status(500).json({ "error": "invalid fields" });
    });
  }
}