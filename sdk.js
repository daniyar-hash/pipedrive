 import AppExtensionsSDK from '@pipedrive/app-extensions-sdk';

// SDK detects identifier from URL and uses default custom UI size
const sdk = await new AppExtensionsSDK().initialize();





// Pass in id manually and provide custom UI size
const sdk = await new AppExtensionsSDK({ identifier: '6689b08e26d9ca867' }) 
  .initialize({ size: { height: 500 } });


  const { status } = await sdk.execute(Command.OPEN_MODAL, {
  type: Modal.CUSTOM_MODAL,
  action_id: '6f842ed8-4714-49f1-a138-e4d852398b79',
  data: {
    item: 'xyz'
  },
});


const { status, id } = await sdk.execute(Command.OPEN_MODAL, {
  type: Modal.DEAL,
  prefill: {
    title: 'Important deal',
  },
})