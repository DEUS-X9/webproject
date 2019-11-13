/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('shop', {
    ID_ITEM: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    ITEM: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    PRIX: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    DESCRIPTION: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    ACTIF: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    ID_CATEGORIE: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    }
  }, {
    timestamps : false,
    tableName: 'shop'
  });
};
