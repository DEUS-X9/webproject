// Imports
var models = require('../models');
var asyncLib = require('async');
var jwtUtils = require('../utils/jwt.utils');

// Constants
const TITLE_LIMIT = 2;
const CONTENT_LIMIT = 4;
const ITEMS_LIMIT = 50;
const ADMIN = 2;

// Routes
module.exports = {
  createItem: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var ITEM = req.body.ITEM;
    var DESCRIPTION = req.body.DESCRIPTION;
    var PRIX = req.body.PRIX;
    var DROIT = req.body.DROIT;
    var ID_CATEGORIE = req.body.ID_CATEGORIE;


    if (ITEM == null || DESCRIPTION == null) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }

    if (ITEM.length <= TITLE_LIMIT || DESCRIPTION.length <= CONTENT_LIMIT) {
      return res.status(400).json({ 'error': 'invalid parameters' });
    }

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          where: { ID_MEMBRE: ID_MEMBRE, DROIT: ADMIN }
        })
          .then(function (userFound) {
            done(null, userFound);
          })
          .catch(function (err) {
            console.log(err);
            return res.status(500).json({ 'error': 'unable to verify user or do not have the right to post' });
          });
      },
      function (userFound, done) {
        if (userFound) {
          models.shop.create({
            ITEM: ITEM,
            PRIX: PRIX,
            DESCRIPTION: DESCRIPTION,
            /*ID_MEMBRE : userFound.ID_MEMBRE,*/
            ACTIF: 1,
            ID_CATEGORIE: ID_CATEGORIE
          })
            .then(function (newItem) {
              done(newItem);
            });
        } else {
            console.log(err);
          res.status(404).json({ 'error': 'user not found' });
        }
      },
    ], function (newItem) {
      if (newItem) {
        return res.status(201).json(newItem);
      } else {
        return res.status(500).json({ 'error': 'cannot post item' });
      }
    });

  },
  listItem: function (req, res) {
    var fields = req.query.fields;
    var limit = parseInt(req.query.limit);
    var offset = parseInt(req.query.offset);
    var order = req.query.order;

    if (limit > ITEMS_LIMIT) {
      limit = ITEMS_LIMIT;
    }

    models.shop.findAll({
      order: [(order != null) ? order.split(':') : ['ITEM', 'ASC']],
      attributes: (fields !== '*' && fields != null) ? fields.split(',') : null,
      limit: (!isNaN(limit)) ? limit : null,
      offset: (!isNaN(offset)) ? offset : null,
      /*include: [{
        model: models.membre,
        attributes: [ 'MAIL' ]
      }]*/
    }).then(function (shop) {
      if (shop) {
        res.status(200).json(shop);
      } else {
        res.status(404).json({ "error": "no item found" });
      }
    }).catch(function (err) {
      console.log(err);
      res.status(500).json({ "error": "invalid fields" });
    });
  }
}