/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('panier', {
    ID_PANIER: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    NOMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'membre',
        key: 'ID_MEMBRE'
      }
    },
    ID_ITEM: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'shop',
        key: 'ID_ITEM'
      }
    }
  }, {
    tableName: 'panier'
  });
};
