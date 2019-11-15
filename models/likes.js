/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('likes', {
    ID_LIKE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'membre',
        key: 'ID_MEMBRE'
      }
    },
    ID_PHOTO: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'photo',
        key: 'ID_PHOTO'
      }
    }
  }, {
    timestamps : false,
    tableName: 'likes'
  });
};
