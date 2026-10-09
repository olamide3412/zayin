// resources/js/fontawesome.js
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
  faUserSecret, faUser, faSearch, faCircle, faFileCirclePlus, faGaugeHigh, faEye,
  faPhone, faCertificate, faXmark, faWifi, faClock, faShield, faThumbsUp, faShieldAlt,
  faCoins, faAngleRight, faMapMarkerAlt, faEnvelope, faGift, faBolt, faCheckCircle,
  faBookOpen, faFileAlt, faBook, faEdit, faComment, faGlobe, faComments, faTruck, faLeaf,
  faHandHoldingUsd, faShoppingBasket, faCreditCard, faBox, faLaptop, faBatteryFull, faHouse,
  // Zayin specific icons
  faSolarPanel, faSatelliteDish, faTv, faWrench, faCheck, faCircleCheck, faCircleInfo,
  faCircleQuestion, faShieldHalved, faShoppingCart, faFileInvoiceDollar, faCalendarCheck,
  faLocationDot, faTrash, faPlus, faMinus, faLock, faFilter, faMagnifyingGlass,
  faChevronUp, faChevronDown, faChevronRight, faChevronLeft, faReceipt, faBuildingColumns,
  faCloudArrowUp, faSpinner, faPlay, faStar, faCopy, faBars, faArrowRight, faArrowLeft,
  faBatteryThreeQuarters, faFireFlameCurved, faSun, faNetworkWired, faVideo, faCalculator
} from '@fortawesome/free-solid-svg-icons';

import {
  faFacebook, faInstagram, faTwitter, faXTwitter, faWhatsapp,
  faTiktok, faYoutube, faLinkedin
} from '@fortawesome/free-brands-svg-icons';

library.add(
  faUserSecret, faUser, faSearch, faCircle, faFileCirclePlus, faGaugeHigh, faEye, faPhone,
  faCertificate, faXmark, faWifi, faClock, faShield, faThumbsUp, faShieldAlt, faCoins,
  faFacebook, faInstagram, faTwitter, faXTwitter, faAngleRight, faMapMarkerAlt,
  faEnvelope, faWhatsapp, faGift, faBolt, faCheckCircle, faBookOpen, faFileAlt, faBook, faEdit,
  faComment, faGlobe, faComments, faTruck, faLeaf, faHandHoldingUsd, faShoppingBasket,
  faCreditCard, faBox, faLaptop, faBatteryFull, faHouse, faTiktok, faYoutube, faLinkedin,
  // Zayin icons
  faSolarPanel, faSatelliteDish, faTv, faWrench, faCheck, faCircleCheck, faCircleInfo,
  faCircleQuestion, faShieldHalved, faShoppingCart, faFileInvoiceDollar, faCalendarCheck,
  faLocationDot, faTrash, faPlus, faMinus, faLock, faFilter, faMagnifyingGlass,
  faChevronUp, faChevronDown, faChevronRight, faChevronLeft, faReceipt, faBuildingColumns,
  faCloudArrowUp, faSpinner, faPlay, faStar, faCopy, faBars, faArrowRight, faArrowLeft,
  faBatteryThreeQuarters, faFireFlameCurved, faSun, faNetworkWired, faVideo, faCalculator
);

export { FontAwesomeIcon };
