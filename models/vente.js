/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vente', {
    ID_PANIER: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    Date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    ID_PANIER_DONNE_LIEU: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'panier',
        key: 'ID_PANIER'
      },
      unique: true
    }
  }, {
    timestamps : false,
    tableName: 'vente'
  });
};
