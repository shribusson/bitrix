console.log("✅ see-all-tasks.js подключен");

BX.ready(function () {
  const allowedUserIds = [1]; // ← твой ID

  const userId = parseInt(BX.message('USER_ID'));
  console.log('������ USER_ID из BX.message →', userId);

  if (!allowedUserIds.includes(userId)) {
    console.log(`⛔ Пользователь ${userId} не в списке`);
    return;
  }

  console.log(`������ Пользователь ${userId} — фильтр MEMBER будет отключён`);

  const originalCall = BX.rest.callMethod;

  BX.rest.callMethod = function(method, params = {}, callback, sendOptions) {
    if (method === 'tasks.task.list' && params.filter) {
      console.log(`������ Пользователь ${userId} → фильтр MEMBER отключен`);
      delete params.filter.MEMBER;
    }
    return originalCall.apply(this, [method, params, callback, sendOptions]);
  };
});

