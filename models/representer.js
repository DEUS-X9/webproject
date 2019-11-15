/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('representer', {
    ID_PHOTO: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'photo',
        key: 'ID_PHOTO'
      }
    },
    ID_ITEM: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'shop',
        key: 'ID_ITEM'
      }
    }
  }, {
    timestamps : false,
    tableName: 'representer'
  });
};
