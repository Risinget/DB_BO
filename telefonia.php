<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Leer y validar JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
if (!is_array($input)) $input = [];


$camposBusqueda = [
    "numero",
];

// Verificar si hay al menos un filtro válido
$todosVacios = true;
foreach ($camposBusqueda as $campo) {
    if (!empty($input[$campo])) {
        $todosVacios = false;
        break;
    }
}
if ($todosVacios) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Debe enviar al menos un parámetro de búsqueda no predeterminado."]);
    exit();
}


$query = <<<'GRAPHQL'
query OperatorLookup(
  $input: OperatorLookupInput!,
  $order: [OrderByInput!]
) {
  marketplace {
    availability {
      operatorLookup(input: $input) {
        operator {
          promotions {
            ...PromotionFields
          }
          ...OperatorFields
          productCategoryPriorities {
            ...ProductCategoryPrioritiesFields
          }
          credits: products(
            where: { productCategories: { in: [MTU_CREDITS] } }
            order: $order
          ) {
            ...ProductSearchFields
          }
          plans: products(
            where: {
              and: [
                { productCategories: { in: [MTU_PLANS_BUNDLES] } }
                { productSubCategories: { in: [MTU_PLANS_BUNDLES_PLAN] } }
              ]
            }
            order: $order
          ) {
            ...ProductSearchFields
          }
          bundles: products(
            where: {
              and: [
                { productCategories: { in: [MTU_PLANS_BUNDLES] } }
                { productSubCategories: { in: [MTU_PLANS_BUNDLES_BUNDLES] } }
              ]
            }
            order: $order
          ) {
            ...ProductSearchFields
          }
        }
      }
    }
  }
}

fragment DurationFields on Duration {
  formatted
  quantity
  unit
}

fragment MoneyFields on Money {
  amount
  currency
  formatted
}

fragment ProductFields on Product {
  id
  title
  description
  countryId
  productCategory
  productSubCategory
  serviceCategory
  subscriptionAvailability
  validity {
    formatted
  }
  frequencies {
    preselected
    frequency {
      ...DurationFields
    }
  }
  value {
    ...MoneyFields
  }
}

fragment ProductDeliverableFields on ProductDeliverable {
  amount
  unit
  deliverableType
}

fragment PromotionFields on Promotion {
  description
  endAt
  terms
}

fragment OperatorFields on Operator {
  countryId
  id
  logoUrl
  name
  priority
}

fragment ProductCategoryPrioritiesFields on ProductCategoryPriority {
  clientProductCategoryOrderKey
  priority
}

fragment ProductSearchFields on Product {
  ...ProductFields
  priority
  summary
  promotions {
    id
    title
  }
  deliverables {
    ...ProductDeliverableFields
  }
  priceDetail {
    basePrice {
      ...MoneyFields
    }
    price {
      ...MoneyFields
    }
    discountDetail {
      title
      description
    }
  }
}
GRAPHQL;

$variables = [
    "input" => [
        "destination" => [
            "type" => "MSISDN",
            "msisdn" => "591" . $input['numero']
        ]
    ],
    "order" => [
        ["by" => "priority", "order" => "ASC"],
        ["by" => "price", "order" => "ASC"]
    ]
];

$body = json_encode([
    "query" => $query,
    "variables" => $variables
], JSON_UNESCAPED_SLASHES);

$curl = curl_init("https://gql.rebtel.com/graphql");
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($curl);

if ($response === false) {
    echo json_encode(["curl_error" => curl_error($curl)]);
    exit();
}

echo $response;
