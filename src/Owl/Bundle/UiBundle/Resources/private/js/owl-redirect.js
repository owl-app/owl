import * as Turbo from '@hotwired/turbo';

function redirect (redirectUrl) {
  let splitPath = redirectUrl.split('#'),
      isTurbo = window.Turbo != undefined ? true : false,
      hash = null;

  if(splitPath.hasOwnProperty(1)) {
    if(window.location.pathname === splitPath[0]) {
      hash = splitPath[1];
    }
  }

  if(isTurbo) {
    Turbo.visit(redirectUrl.trim()+(hash !== null ? '#'+hash : ''))
    
    return;
  }

  if(hash !== null) {
    window.location.hash = hash;
    location.reload();

    return;
  }
    
  window.location.href = redirectUrl;
}

export default redirect;
